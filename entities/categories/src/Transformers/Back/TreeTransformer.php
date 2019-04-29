<?php

namespace InetStudio\CategoriesPackage\Categories\Transformers\Back;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\TreeTransformerContract;

/**
 * Class TreeTransformer.
 */
class TreeTransformer extends TransformerAbstract implements TreeTransformerContract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'items',
    ];

    /**
     * Трансформация данных.
     *
     * @param  CategoryModelContract  $item
     *
     * @return array
     */
    public function transform(CategoryModelContract $item): array
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'href' => $item['href'],
        ];
    }

    /**
     * Включаем дочерние объекты в трансформацию.
     *
     * @param  CategoryModelContract  $item
     *
     * @return FractalCollection
     */
    public function includeItems(CategoryModelContract $item): FractalCollection
    {
        return new FractalCollection($item['children'], $this);
    }

    /**
     * Обработка коллекции объектов.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection
    {
        return new FractalCollection($items, $this);
    }
}
