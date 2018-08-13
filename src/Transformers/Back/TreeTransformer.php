<?php

namespace InetStudio\Categories\Transformers\Back;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Transformers\Back\TreeTransformerContract;

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
     * Подготовка данных для отображения дерева.
     *
     * @param CategoryModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(CategoryModelContract $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'href' => $item->href,
        ];
    }

    /**
     * Включаем дочерние объекты в трансформацию.
     *
     * @param CategoryModelContract $item
     *
     * @return FractalCollection
     */
    public function includeItems(CategoryModelContract $item)
    {
        return new FractalCollection($item->children, $this);
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
