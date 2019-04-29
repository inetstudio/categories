<?php

namespace InetStudio\CategoriesPackage\Categories\Http\Responses\Back\Resource;

use Illuminate\Http\Request;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract
{
    /**
     * @var CategoryModelContract
     */
    protected $item;

    /**
     * SaveResponse constructor.
     *
     * @param  CategoryModelContract  $item
     */
    public function __construct(CategoryModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении страницы.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        $item = $this->item->fresh();

        return response()->redirectToRoute(
            'back.categories.edit',
            [
                $item['id'],
            ]
        );
    }
}
