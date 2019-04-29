<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\Utility;

use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Interface SuggestionTransformerContract.
 */
interface SuggestionTransformerContract
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
     * @param $pages
     *
     * @return FractalCollection
     */
    public function transformCollection($pages): FractalCollection;
}
