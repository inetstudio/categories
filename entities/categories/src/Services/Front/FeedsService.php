<?php

namespace InetStudio\CategoriesPackage\Categories\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Services\Front\FeedsServiceContract;

/**
 * Class FeedsService.
 */
class FeedsService extends BaseService implements FeedsServiceContract
{
    /**
     * FeedsService constructor.
     *
     * @param  CategoryModelContract  $model
     */
    public function __construct(CategoryModelContract $model)
    {
        parent::__construct($model);
    }
}
