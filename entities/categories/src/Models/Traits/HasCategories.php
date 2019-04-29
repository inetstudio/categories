<?php

namespace InetStudio\CategoriesPackage\Categories\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Trait HasCategories.
 */
trait HasCategories
{
    use HasCategoriesCollection;

    /**
     * The Queued Categories.
     *
     * @var array
     */
    protected $queuedCategories = [];

    /**
     * Get Category class name.
     *
     * @return string
     *
     * @throws BindingResolutionException
     */
    public function getCategoryClassName(): string
    {
        $model = app()->make(CategoryModelContract::class);

        return get_class($model);
    }

    /**
     * Get all attached categories to the model.
     *
     * @return MorphToMany
     *
     * @throws BindingResolutionException
     */
    public function categories(): MorphToMany
    {
        $className = $this->getCategoryClassName();

        return $this->morphToMany($className, 'categorizable')->withTimestamps();
    }

    /**
     * Attach the given category(ies) to the model.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @throws BindingResolutionException
     */
    public function setCategoriesAttribute($categories): void
    {
        if (! $this->exists) {
            $this->queuedCategories = $categories;

            return;
        }

        $this->attachCategories($categories);
    }

    /**
     * Boot the categorygable trait for a model.
     */
    public static function bootHasCategories()
    {
        static::created(
            function (Model $categorygableModel) {
                if ($categorygableModel->queuedCategories) {
                    $categorygableModel->attachCategories($categorygableModel->queuedCategories);
                    $categorygableModel->queuedCategories = [];
                }
            }
        );

        static::deleted(
            function (Model $categorygableModel) {
                $categorygableModel->syncCategories(null);
            }
        );
    }

    /**
     * Get the category list.
     *
     * @param  string  $keyColumn
     *
     * @return array
     *
     * @throws BindingResolutionException
     */
    public function categoryList(string $keyColumn = 'slug'): array
    {
        return $this->categories()->pluck('name', $keyColumn)->toArray();
    }

    /**
     * Scope query with all the given categories.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAllCategories(Builder $query, $categories, string $column = 'slug'): Builder
    {
        $categories = $this->isCategoriesStringBased($categories)
            ? $categories : $this->hydrateCategories($categories)->pluck($column);

        collect($categories)->each(
            function ($category) use ($query, $column) {
                $query->whereHas(
                    'categories',
                    function (Builder $query) use ($category, $column) {
                        return $query->where($column, $category);
                    }
                );
            }
        );

        return $query;
    }

    /**
     * Scope query with any of the given categories.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAnyCategories(Builder $query, $categories, string $column = 'slug'): Builder
    {
        $categories = $this->isCategoriesStringBased($categories)
            ? $categories : $this->hydrateCategories($categories)->pluck($column);

        return $query->whereHas(
            'categories',
            function (Builder $query) use ($categories, $column) {
                $query->whereIn($column, (array) $categories);
            }
        );
    }

    /**
     * Scope query with any of the given categories.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithCategories(Builder $query, $categories, string $column = 'slug'): Builder
    {
        return $this->scopeWithAnyCategories($query, $categories, $column);
    }

    /**
     * Scope query without the given categories.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithoutCategories(Builder $query, $categories, string $column = 'slug'): Builder
    {
        $categories = $this->isCategoriesStringBased($categories)
            ? $categories : $this->hydrateCategories($categories)->pluck($column);

        return $query->whereDoesntHave(
            'categories',
            function (Builder $query) use ($categories, $column) {
                $query->whereIn($column, (array) $categories);
            }
        );
    }

    /**
     * Scope query without any categories.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithoutAnyCategories(Builder $query): Builder
    {
        return $query->doesntHave('categories');
    }

    /**
     * Attach the given category(ies) to the model.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function attachCategories($categories): self
    {
        $this->setCategories($categories, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given category(ies) to the model.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract|null  $categories
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function syncCategories($categories): self
    {
        $this->setCategories($categories, 'sync');

        return $this;
    }

    /**
     * Detach the given category(ies) from the model.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function detachCategories($categories): self
    {
        $this->setCategories($categories, 'detach');

        return $this;
    }

    /**
     * Set the given category(ies) to the model.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     * @param  string  $action
     *
     * @throws BindingResolutionException
     */
    protected function setCategories($categories, string $action): void
    {
        // Fix exceptional event name
        $event = $action === 'syncWithoutDetaching' ? 'attach' : $action;

        // Hydrate Categories
        $categories = $this->hydrateCategories($categories)->pluck('id')->toArray();

        // Fire the Category syncing event
        static::$dispatcher->dispatch('inetstudio.categories.'.$event.'ing', [$this, $categories]);

        // Set Categories
        $this->categories()->$action($categories);

        // Fire the Category synced event
        static::$dispatcher->dispatch('inetstudio.categories.'.$event.'ed', [$this, $categories]);
    }

    /**
     * Hydrate categories.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    protected function hydrateCategories($categories): Collection
    {
        $isCategoriesStringBased = $this->isCategoriesStringBased($categories);
        $isCategoriesIntBased = $this->isCategoriesIntBased($categories);
        $field = $isCategoriesStringBased ? 'slug' : 'id';
        $className = $this->getCategoryClassName();

        return $isCategoriesStringBased || $isCategoriesIntBased
            ? $className::query()->whereIn($field, (array) $categories)->get() : collect($categories);
    }
}
