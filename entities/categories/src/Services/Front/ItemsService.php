<?php

namespace InetStudio\CategoriesPackage\Categories\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\AdminPanel\Base\Services\Traits\SlugsServiceTrait;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Services\Front\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    use SlugsServiceTrait;

    /**
     * ItemsService constructor.
     *
     * @param  CategoryModelContract  $model
     */
    public function __construct(CategoryModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Родительский объект.
     *
     * @param $category
     * @param  array  $params
     *
     * @return mixed
     */
    public function getParentItem($category, array $params = [])
    {
        return $this->model
            ->buildQuery($params)
            ->where('id', $category['parent_id'])
            ->first();
    }

    /**
     * Дочерние объекты.
     *
     * @param $category
     * @param  array  $params
     *
     * @return mixed
     */
    public function getSubItems($category, array $params = [])
    {
        return $this->model
            ->buildQuery($params)
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1)
            ->descendantsOf($category['id']);
    }
}
