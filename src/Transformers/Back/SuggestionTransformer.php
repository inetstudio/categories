<?php

namespace InetStudio\Categories\Transformers\Back;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Transformers\Back\SuggestionTransformerContract;

/**
 * Class SuggestionTransformer.
 */
class SuggestionTransformer extends TransformerAbstract implements SuggestionTransformerContract
{
    /**
     * @var string
     */
    private $type;

    /**
     * PagesSiteMapTransformer constructor.
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Подготовка данных для отображения в выпадающих списках.
     *
     * @param CategoryModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(CategoryModelContract $item): array
    {
        if ($this->type && $this->type == 'autocomplete') {
            $modelClass = get_class($item);

            return [
                'value' => $item->title,
                'data' => [
                    'id' => $item->id,
                    'type' => $modelClass,
                    'title' => $item->title,
                    'path' => parse_url($item->href, PHP_URL_PATH),
                    'href' => $item->href,
                ],
            ];
        } else {
            return [
                'id' => $item->id,
                'name' => $item->title,
            ];
        }
    }

    /**
     * Обработка коллекции страниц.
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
