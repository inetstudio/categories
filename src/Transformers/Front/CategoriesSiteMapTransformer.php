<?php

namespace InetStudio\Categories\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract;

/**
 * Class CategoriesSiteMapTransformer.
 */
class CategoriesSiteMapTransformer extends TransformerAbstract implements CategoriesSiteMapTransformerContract
{
    /**
     * Подготовка данных для отображения в карте сайта.
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
            'loc' => $item->href,
            'lastmod' => $item->updated_at->toW3cString(),
            'priority' => '0.8',
            'freq' => 'monthly',
        ];
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
