<?php

namespace InetStudio\Categories\Http\Responses\Back\Categories;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract, Responsable
{
    /**
     * @var CategoryModelContract
     */
    private $item;

    /**
     * SaveResponse constructor.
     *
     * @param CategoryModelContract $item
     */
    public function __construct(CategoryModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении страницы.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return RedirectResponse
     */
    public function toResponse($request): RedirectResponse
    {
        return response()->redirectToRoute('back.categories.edit', [
            $this->item->fresh()->id,
        ]);
    }
}
