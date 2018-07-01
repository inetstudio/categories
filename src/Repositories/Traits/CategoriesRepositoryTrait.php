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
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByCategory(string $slug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery($extColumns, $with)->withCategories($slug);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsFromCategories($categories, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery($extColumns, $with)->withCategories($categories, 'categories.slug');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }
}
