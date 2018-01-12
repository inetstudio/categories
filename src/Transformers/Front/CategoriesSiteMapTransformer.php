<?php

namespace InetStudio\Categories\Transformers\Front;

use League\Fractal\TransformerAbstract;
use InetStudio\Categories\Models\CategoryModel;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class CategoriesSiteMapTransformer
 * @package InetStudio\Categories\Transformers\Front
 */
class CategoriesSiteMapTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param CategoryModel $category
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(CategoryModel $category): array
    {
        return [
            'loc' => $category->href,
            'lastmod' => $category->updated_at->toW3cString(),
            'priority' => '0.8',
            'freq' => 'monthly',
        ];
    }

    /**
     * Обработка коллекции категорий.
     *
     * @param $categories
     *
     * @return FractalCollection
     */
    public function transformCollection($categories): FractalCollection
    {
        return new FractalCollection($categories, $this);
    }
}
