<?php

namespace InetStudio\Categories\Http\Controllers\Back\Traits;

use InetStudio\Categories\Models\CategoryModel;

trait CategoriesManipulationsTrait
{
    /**
     * Сохраняем категории.
     *
     * @param $item
     * @param $request
     */
    private function saveCategories($item, $request)
    {
        if ($request->filled('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize(CategoryModel::whereIn('id', $categories)->get());
        } else {
            $item->uncategorize($item->categories);
        }
    }
}
