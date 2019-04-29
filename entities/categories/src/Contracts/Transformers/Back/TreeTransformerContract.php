<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back;

use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Interface TreeTransformerContract.
 */
interface TreeTransformerContract
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
     * Включаем дочерние объекты в трансформацию.
     *
     * @param  CategoryModelContract  $item
     *
     * @return FractalCollection
     */
    public function includeItems(CategoryModelContract $item): FractalCollection;

    /**
     * Обработка коллекции объектов.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection;
}
