<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Services\Front;

use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;

/**
 * Interface ItemsServiceContract.
 */
interface ItemsServiceContract extends BaseServiceContract
{
    /**
     * Родительский объект.
     *
     * @param $category
     * @param  array  $params
     *
     * @return mixed
     */
    public function getParentItem($category, array $params = []);

    /**
     * Дочерние объекты.
     *
     * @param $category
     * @param  array  $params
     *
     * @return mixed
     */
    public function getSubItems($category, array $params = []);
}
