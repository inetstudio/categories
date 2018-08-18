<?php

namespace InetStudio\Categories\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract;

/**
 * Class CategoriesRepository.
 */
class CategoriesRepository implements CategoriesRepositoryContract
{
    /**
     * @var CategoryModelContract
     */
    public $model;

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
     * Получаем модель репозитория.
     *
     * @return CategoryModelContract
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Возвращаем пустой объект по id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getEmptyObjectById(int $id = 0)
    {
        return $this->model::select(['id'])->where('id', '=', $id)->first();
    }

    /**
     * Возвращаем объект по id, либо создаем новый.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getItemByID(int $id = 0): CategoryModelContract
    {
        return $this->model::find($id) ?? new $this->model;
    }

    /**
     * Возвращаем удаленный объект по id, либо пустой.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getTrashedItemByID(int $id = 0): CategoryModelContract
    {
        return $this->model::onlyTrashed()->find($id) ?? new $this->model;
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)
            ->whereIn('id', (array) $ids);

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param array $data
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function save(array $data = [], int $id = 0): CategoryModelContract
    {
        $item = $this->getItemByID($id);
        $item->fill($data);
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
    public function destroy($id = 0): ?bool
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
     * @param array $conditions
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function searchItems(array $conditions, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)->where($conditions);

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param array $properties
     * @param array $with
     * @param array $sort
     * 
     * @return mixed
     */
    public function getAllItems(array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort);

        return $builder->get();
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $properties
     * @param array $with
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, array $properties = [], array $with = [])
    {
        $builder = $this->getItemsQuery($properties, $with)
            ->whereSlug($slug);

        $item = $builder->first();

        return $item;
    }

    /**
     * Родительский объект.
     *
     * @param $item
     * @param array $properties
     * @param array $with
     *
     * @return mixed
     */
    public function getParentItem($item, array $properties = [], array $with = [])
    {
        $builder = $this->getItemsQuery($properties, $with)
            ->where('id', $item['parent_id']);

        $item = $builder->first();

        return $item;
    }

    /**
     * Подобъекты.
     *
     * @param $parentItem
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getSubItems($parentItem, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1);

        return $builder->descendantsOf($parentItem['id']);
    }

    /**
     * Возвращаем запрос на получение объектов.
     *
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return Builder
     */
    public function getItemsQuery(array $properties = [], array $with = [], array $sort = []): Builder
    {
        $defaultColumns = ['id', 'name', 'slug', 'created_at'];

        $relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            },
        ];

        $builder = $this->model::select(array_merge($defaultColumns, $properties))
            ->with(array_intersect_key($relations, array_flip($with)));

        foreach ($sort as $column => $direction) {
            $builder->orderBy($column, $direction);
        }

        return $builder;
    }
}
