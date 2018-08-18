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
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getItemsByCategory(string $slug, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)->withCategories($slug);

        return $builder->get();
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getItemsFromCategories($categories, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)->withCategories($categories, 'categories.slug');

        return $builder->get();
    }

    /**
     * Получаем объекты из любых категорий.
     *
     * @param $categories
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getItemsByAnyCategory($categories, array $properties = [], array $with = [], array $sort = [])
    {
        $builder = $this->getItemsQuery($properties, $with, $sort)->withAnyCategories($categories, 'categories.slug');

        return $builder->get();
    }
}
