<?php

namespace InetStudio\Categories\Services\Front\Traits;

/**
 * Trait CategoriesServiceTrait.
 */
trait CategoriesServiceTrait
{
    /**
     * Получаем объекты по категории.
     *
     * @param string $categorySlug
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByCategory(string $categorySlug, array $params = [])
    {
        return $this->repository->getItemsByCategory($categorySlug, $params);
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsFromCategories($categories, array $params = [])
    {
        return $this->repository->getItemsFromCategories($categories, $params);
    }

    /**
     * Получаем объекты из любых категорий.
     *
     * @param $categories
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByAnyCategory($categories, array $params = [])
    {
        return $this->repository->getItemsByAnyCategory($categories, $params);
    }
}
