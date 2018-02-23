<?php

namespace InetStudio\Categories\Repositories\Back\Traits;

/**
 * Trait CategoriesRepositoryTrait.
 */
trait CategoriesRepositoryTrait
{
    /**
     * Получаем объекты по категории.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByCategory(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'title', 'description', 'slug'])
            ->with(['meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            }, 'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            }])
            ->withCategories($slug);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }
}
