<?php

namespace InetStudio\Categories\Http\Responses\Back\Categories;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract;

/**
 * Class FormResponse.
 */
class FormResponse implements FormResponseContract, Responsable
{
    /**
     * @var array
     */
    private $data;

    /**
     * FormResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращаем ответ при открытии формы страницы.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function toResponse($request): View
    {
        return view('admin.module.categories::back.pages.form', $this->data);
    }
}
