<?php

namespace InetStudio\Categories\Services\Back;

use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract;

/**
 * Class CategoriesService.
 */
class CategoriesService implements CategoriesServiceContract
{
    public function attachToObject($request, $item)
    {
        if ($request->filled('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize(CategoryModel::whereIn('id', $categories)->get());
        } else {
            $item->uncategorize($item->categories);
        }
    }
}
