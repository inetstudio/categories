<?php

namespace InetStudio\Categories\Repositories\Back;

use Illuminate\Support\Collection;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;
use InetStudio\Categories\Contracts\Repositories\Back\CategoriesRepositoryContract;

/**
 * Class CategoriesRepository.
 */
class CategoriesRepository implements CategoriesRepositoryContract
{
    /**
     * @var CategoryModelContract
     */
    private $model;

    /**
     * CategoriesRepository constructor.
     *
     * @param CategoryModelContract $model
     */
    public function __construct(CategoryModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * Возвращаем объект по id, либо создаем новый.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getItemByID(int $id): CategoryModelContract
    {
        if (! (! is_null($id) && $id > 0 && $item = $this->model::find($id))) {
            $item = $this->model;
        }

        return $item;
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'name', 'slug'])
            ->whereIn('id', (array) $ids);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param SaveCategoryRequestContract $request
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function save(SaveCategoryRequestContract $request, int $id): CategoryModelContract
    {
        $item = $this->getItemByID($id);

        $item->name = strip_tags($request->get('name'));
        $item->slug = strip_tags($request->get('slug'));
        $item->title = strip_tags($request->get('title'));
        $item->description = strip_tags($request->input('description.text'));
        $item->content = $request->input('content.text');
        $item->save();

        return $item;
    }

    /**
     * Удаляем объект.
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy($id): ?bool
    {
        return $this->getItemByID($id)->delete();
    }

    /**
     * Перестраиваем дерево объектов.
     *
     * @param array $data
     *
     * @return int
     */
    public function rebuildTree(array $data): int
    {
        return $this->model::defaultOrder()->rebuildTree($data);
    }

    /**
     * Получаем дерево объектов.
     *
     * @return Collection
     */
    public function getTree(): Collection
    {
        return $this->model::defaultOrder()->get()->toTree();
    }

    /**
     * Ищем объекты.
     *
     * @param string $field
     * @param $value
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function searchItemsByField(string $field, string $value, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'name as title', 'slug'])
            ->where($field, 'LIKE', '%'.$value.'%');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllItems(bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'name', 'slug', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'parent_id', 'slug', 'name', 'title', 'description', 'content'])
            ->with(['meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            }, 'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            }])
            ->whereSlug($slug);

        if ($returnBuilder) {
            return $builder;
        }

        $item = $builder->first();

        return $item;
    }

    /**
     * Родительский объект.
     *
     * @param CategoryModelContract $item
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getParentItem(CategoryModelContract $item, bool $returnBuilder = false)
    {
        if ($returnBuilder) {
            $builder = $this->model::select(['id', 'parent_id', 'slug', 'name', 'title', 'description', 'content'])
                ->with(['meta' => function ($query) {
                    $query->select(['metable_id', 'metable_type', 'key', 'value']);
                }, 'media' => function ($query) {
                    $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                }])
                ->where('id', $item->parent_id);

            return $builder;
        }

        return $item->parent;
    }

    /**
     * Подобъекты.
     *
     * @param CategoryModelContract $parentItem
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getSubItems(CategoryModelContract $parentItem, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'name', 'slug', 'title'])
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1);

        if ($returnBuilder) {
            $builder = $builder->where('parent_id', $parentItem->id);

            return $builder;
        }

        return $builder->descendantsOf($parentItem->id);
    }
}
