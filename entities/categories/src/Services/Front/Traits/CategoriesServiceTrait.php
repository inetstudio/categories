<?php

namespace InetStudio\CategoriesPackage\Categories\Services\Front\Traits;

use Illuminate\Database\Eloquent\Collection;

/**
 * Trait CategoriesServiceTrait.
 */
trait CategoriesServiceTrait
{
    /**
     * Получаем объекты по категории.
     *
     * @param  string  $slug
     * @param  array  $params
     *
     * @return Collection
     */
    public function getItemsByCategory(string $slug, array $params = []): Collection
    {
        return $this->model
            ->buildQuery($params)
            ->withCategories($slug)
            ->get();
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param  array  $params
     *
     * @return Collection
     */
    public function getItemsFromCategories($categories, array $params = []): Collection
    {
        return $this->model
            ->buildQuery($params)
            ->withCategories($categories, 'categories.slug')
            ->get();
    }

    /**
     * Получаем объекты из любых категорий.
     *
     * @param $categories
     * @param  array  $params
     *
     * @return Collection
     */
    public function getItemsByAnyCategory($categories, array $params = []): Collection
    {
        return $this->model
            ->buildQuery($params)
            ->withAnyCategories($categories, 'categories.slug')
            ->get();
    }
}
