<?php

namespace InetStudio\Categories\Services\Front;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use InetStudio\Categories\Models\CategoryModel;

class CategoriesService
{
    /**
     * Получаем категорию по slug.
     *
     * @param string $slug
     * @return CategoryModel
     */
    public function getCategoryBySlug(string $slug): CategoryModel
    {
        $cacheKey = 'CategoriesService_getCategoryBySlug_'.md5($slug);

        $categories = Cache::tags(['categories'])->remember($cacheKey, 1440, function () use ($slug) {
            return CategoryModel::select(['id', 'parent_id', 'slug', 'name', 'title', 'description', 'content'])
                ->with(['meta' => function ($query) {
                    $query->select(['metable_id', 'metable_type', 'key', 'value']);
                }, 'media' => function ($query) {
                    $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                }])
                ->whereSlug($slug)
                ->get();
        });

        if ($categories->count() == 0) {
            abort(404);
        }

        return $categories->first();
    }

    /**
     * Родительская категория.
     *
     * @param CategoryModel $category
     * @return CategoryModel
     */
    public function getParentCategory(CategoryModel $category): CategoryModel
    {
        $cacheKey = 'CategoriesService_getParentCategory_'.$category->id;

        return Cache::tags(['categories'])->remember($cacheKey, 1440, function () use ($category) {
            $parentCategory = $category->parent;

            return ($parentCategory) ? $parentCategory : $category;
        });
    }

    /**
     * Подкатегории.
     *
     * @param CategoryModel $parentCategory
     * @return Collection
     */
    public function getSubCategories(CategoryModel $parentCategory): Collection
    {
        $cacheKey = 'CategoriesService_getSubCategories_'.$parentCategory->id;

        return Cache::tags(['categories'])->remember($cacheKey, 1440, function () use ($parentCategory) {
            return CategoryModel::select(['id', 'name', 'slug', 'title'])
                ->defaultOrder()
                ->withDepth()
                ->having('depth', '=', 1)
                ->descendantsOf($parentCategory->id);
        });
    }
}
