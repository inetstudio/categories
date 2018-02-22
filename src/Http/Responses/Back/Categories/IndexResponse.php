<?php

namespace InetStudio\Categories\Http\Responses\Back\Categories;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\IndexResponseContract;

/**
 * Class IndexResponse.
 */
class IndexResponse implements IndexResponseContract, Responsable
{
    /**
     * @var array
     */
    private $data;

    /**
     * IndexResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращаем ответ при открытии списка страниц.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function toResponse($request): View
    {
        return view('admin.module.categories::back.pages.index', $this->data);
    }
}
