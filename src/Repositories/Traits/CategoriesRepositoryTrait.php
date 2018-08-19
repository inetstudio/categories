<?php

namespace InetStudio\Categories\Repositories\Traits;

/**
 * Trait CategoriesRepositoryTrait.
 */
trait CategoriesRepositoryTrait
{
    /**
     * Получаем объекты по категории.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByCategory(string $slug, array $params = [])
    {
        $builder = $this->getItemsQuery($params)->withCategories($slug);

        return $builder->get();
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
        $builder = $this->getItemsQuery($params)->withCategories($categories, 'categories.slug');

        return $builder->get();
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
        $builder = $this->getItemsQuery($params)->withAnyCategories($categories, 'categories.slug');

        return $builder->get();
    }
}
