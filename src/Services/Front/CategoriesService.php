<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use InetStudio\Categories\Models\CategoryModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Services\CategoriesServiceContract;
use InetStudio\Categories\Transformers\Front\CategoriesSiteMapTransformer;

/**
 * Class CategoriesService
 * @package InetStudio\Categories\Services\Front
 */
class CategoriesService implements CategoriesServiceContract
{
    /**
     * Получаем категорию по slug.
     *
     * @param string $slug
     *
     * @return CategoryModel
     */
    public function getCategoryBySlug(string $slug): CategoryModel
    {
        $cacheKey = 'CategoriesService_getCategoryBySlug_'.md5($slug);

        //$categories = Cache::tags(['categories'])->remember($cacheKey, 1440, function () use ($slug) {
        $categories = Cache::remember($cacheKey, 1440, function () use ($slug) {
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
     *
     * @return CategoryModel
     */
    public function getParentCategory(CategoryModel $category): CategoryModel
    {
        $cacheKey = 'CategoriesService_getParentCategory_'.md5($category->id);

        return Cache::remember($cacheKey, 1440, function () use ($category) {
            $parentCategory = $category->parent;

            return ($parentCategory) ? $parentCategory : $category;
        });
    }

    /**
     * Подкатегории.
     *
     * @param CategoryModel $parentCategory
     *
     * @return Collection
     */
    public function getSubCategories(CategoryModel $parentCategory): Collection
    {
        $cacheKey = 'CategoriesService_getSubCategories_'.md5($parentCategory->id);

        return Cache::remember($cacheKey, 1440, function () use ($parentCategory) {
            return CategoryModel::select(['id', 'name', 'slug', 'title'])
                ->defaultOrder()
                ->withDepth()
                ->having('depth', '=', 1)
                ->descendantsOf($parentCategory->id);
        });
    }

    /**
     * Получаем информацию по категориям для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $categories = CategoryModel::select(['slug', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        $resource = (new CategoriesSiteMapTransformer())->transformCollection($categories);

        return $this->serializeToArray($resource);
    }

    /**
     * Преобразовываем данные в массив.
     *
     * @param $resource
     *
     * @return array
     */
    private function serializeToArray($resource): array
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
