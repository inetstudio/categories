<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Front\Sitemap;

use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Interface ItemTransformerContract.
 */
interface ItemTransformerContract
{
    /**
     * Трансформация данных.
     *
     * @param  CategoryModelContract  $item
     *
     * @return array
     */
    public function transform(CategoryModelContract $item): array;

    /**
     * Обработка коллекции объектов.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection;
}
