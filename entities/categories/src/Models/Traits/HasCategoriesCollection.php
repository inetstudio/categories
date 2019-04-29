<?php

namespace InetStudio\CategoriesPackage\Categories\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Trait HasCategoriesCollection.
 */
trait HasCategoriesCollection
{
    /**
     * Determine if the model has any the given categories.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return bool
     */
    public function hasCategory($categories): bool
    {
        if ($this->isCategoriesStringBased($categories)) {
            return ! $this->categories->pluck('slug')->intersect((array) $categories)->isEmpty();
        }

        if ($this->isCategoriesIntBased($categories)) {
            return ! $this->categories->pluck('id')->intersect((array) $categories)->isEmpty();
        }

        if ($categories instanceof CategoryModelContract) {
            return $this->categories->contains('slug', $categories['slug']);
        }

        if ($categories instanceof Collection) {
            return ! $categories->intersect($this->categories->pluck('slug'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given categories.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return bool
     */
    public function hasAnyCategory($categories): bool
    {
        return $this->hasCategory($categories);
    }

    /**
     * Determine if the model has all of the given categories.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return bool
     */
    public function hasAllCategories($categories): bool
    {
        if ($this->isCategoriesStringBased($categories)) {
            $categories = (array) $categories;

            return $this->categories->pluck('slug')->intersect($categories)->count() == count($categories);
        }

        if ($this->isCategoriesIntBased($categories)) {
            $categories = (array) $categories;

            return $this->categories->pluck('id')->intersect($categories)->count() == count($categories);
        }

        if ($categories instanceof CategoryModelContract) {
            return $this->categories->contains('slug', $categories['slug']);
        }

        if ($categories instanceof Collection) {
            return $this->categories->intersect($categories)->count() == $categories->count();
        }

        return false;
    }

    /**
     * Determine if the given category(ies) are string based.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return bool
     */
    protected function isCategoriesStringBased($categories): bool
    {
        return is_string($categories) || (is_array($categories) && isset($categories[0]) && is_string($categories[0]));
    }

    /**
     * Determine if the given category(ies) are integer based.
     *
     * @param  int|string|array|ArrayAccess|CategoryModelContract  $categories
     *
     * @return bool
     */
    protected function isCategoriesIntBased($categories): bool
    {
        return is_int($categories) || (is_array($categories) && isset($categories[0]) && is_int($categories[0]));
    }
}
