<?php

namespace InetStudio\Categories\Traits;

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
        if ($request->has('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize(CategoryModel::whereIn('id', $categories)->get());
        } else {
            $item->uncategorize($item->categories);
        }
    }
}
