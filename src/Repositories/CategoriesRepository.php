<?php

namespace InetStudio\Categories\Repositories;

use Illuminate\Support\Collection;
use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\AdminPanel\Repositories\Traits\SlugsRepositoryTrait;
use InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract;

/**
 * Class CategoriesRepository.
 */
class CategoriesRepository extends BaseRepository implements CategoriesRepositoryContract
{
    use SlugsRepositoryTrait;
    
    /**
     * CategoriesRepository constructor.
     *
     * @param CategoryModelContract $model
     */
    public function __construct(CategoryModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'name', 'slug', 'created_at'];
        $this->relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            },
        ];
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
     * Родительский объект.
     *
     * @param $item
     * @param array $params
     *
     * @return mixed
     */
    public function getParentItem($item, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->where('id', $item['parent_id']);

        $item = $builder->first();

        return $item;
    }

    /**
     * Подобъекты.
     *
     * @param $parentItem
     * @param array $params
     *
     * @return mixed
     */
    public function getSubItems($parentItem, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1);

        return $builder->descendantsOf($parentItem['id']);
    }
}
