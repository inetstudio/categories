<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use InetStudio\Categories\Models\CategoryModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract;
use InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract;

/**
 * Class CategoriesService.
 */
class CategoriesService implements CategoriesServiceContract
{
    /**
     * Получаем категорию по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public static function getCategoryBySlug(string $slug, bool $returnBuilder = false)
    {
        $builder = CategoryModel::select(['id', 'parent_id', 'slug', 'name', 'title', 'description', 'content'])
            ->with(['meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            }, 'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            }])
            ->whereSlug($slug);

        if ($returnBuilder) {
            return $builder;
        }

        $category = $builder->first();

        if (! $category) {
            abort(404);
        }

        return $category;
    }

    /**
     * Родительская категория.
     *
     * @param CategoryModel $category
     * @param bool $returnBuilder
     *
     * @return CategoryModel
     */
    public static function getParentCategory(CategoryModel $category, bool $returnBuilder = false)
    {
        if ($returnBuilder) {
            $builder = CategoryModel::select(['id', 'parent_id', 'slug', 'name', 'title', 'description', 'content'])
                ->with(['meta' => function ($query) {
                    $query->select(['metable_id', 'metable_type', 'key', 'value']);
                }, 'media' => function ($query) {
                    $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                }])
                ->where('id', $category->parent_id);

            return $builder;
        }

        return $category->parent;
    }

    /**
     * Подкатегории.
     *
     * @param CategoryModel $parentCategory
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public static function getSubCategories(CategoryModel $parentCategory, bool $returnBuilder = false)
    {
        $builder = CategoryModel::select(['id', 'name', 'slug', 'title'])
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1)
            ->descendantsOf($parentCategory->id);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем информацию по категориям для карты сайта.
     *
     * @return array
     */
    public static function getSiteMapItems(): array
    {
        $categories = CategoryModel::select(['slug', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        $resource = (app()->make(CategoriesSiteMapTransformerContract::class))->transformCollection($categories);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
