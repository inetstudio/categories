<?php

namespace InetStudio\CategoriesPackage\Categories\Services\Front\Traits;

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
     * @return mixed
     */
    public function getItemsByCategory(string $slug, array $params = [])
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
     * @return mixed
     */
    public function getItemsFromCategories($categories, array $params = [])
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
     * @return mixed
     */
    public function getItemsByAnyCategory($categories, array $params = [])
    {
        return $this->model
            ->buildQuery($params)
            ->withAnyCategories($categories, 'categories.slug')
            ->get();
    }
}
