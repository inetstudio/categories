<?php

namespace InetStudio\CategoriesPackage\Categories\Transformers\Back\Utility;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\Utility\SuggestionTransformerContract;

/**
 * Class SuggestionTransformer.
 */
class SuggestionTransformer extends TransformerAbstract implements SuggestionTransformerContract
{
    /**
     * @var string
     */
    protected $type;

    /**
     * SuggestionTransformer constructor.
     *
     * @param $type
     */
    public function __construct(string $type = '')
    {
        $this->type = $type;
    }

    /**
     * Трансформация данных.
     *
     * @param  CategoryModelContract  $item
     *
     * @return array
     */
    public function transform(CategoryModelContract $item): array
    {
        return ($this->type == 'autocomplete')
            ? [
                'value' => $item['name'],
                'data' => [
                    'id' => $item['id'],
                    'type' => get_class($item),
                    'title' => $item['name'],
                    'path' => parse_url($item['href'], PHP_URL_PATH),
                    'href' => $item['href'],
                ],
            ]
            : [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
    }

    /**
     * Обработка коллекции объектов.
     *
     * @param $pages
     *
     * @return FractalCollection
     */
    public function transformCollection($pages): FractalCollection
    {
        return new FractalCollection($pages, $this);
    }
}
