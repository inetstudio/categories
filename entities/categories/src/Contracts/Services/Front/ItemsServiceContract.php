<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Services\Front;

use Illuminate\Database\Eloquent\Collection;
use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

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
     * @return CategoryModelContract|null
     */
    public function getParentItem($category, array $params = []): ?CategoryModelContract;

    /**
     * Дочерние объекты.
     *
     * @param $category
     * @param  array  $params
     *
     * @return Collection
     */
    public function getSubItems($category, array $params = []): Collection;
}
