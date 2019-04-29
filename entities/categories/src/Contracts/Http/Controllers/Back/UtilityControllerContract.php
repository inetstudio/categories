<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Http\Controllers\Back;

use Illuminate\Http\Request;
use InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\ItemsServiceContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\UtilityServiceContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;

/**
 * Interface UtilityControllerContract.
 */
interface UtilityControllerContract
{
    /**
     * Получаем slug для модели по строке.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  Request  $request
     *
     * @return SlugResponseContract
     */
    public function getSlug(ItemsServiceContract $itemsService, Request $request): SlugResponseContract;

    /**
     * Возвращаем объекты для поля.
     *
     * @param  UtilityServiceContract  $utilityService
     * @param  Request  $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(UtilityServiceContract $utilityService, Request $request): SuggestionsResponseContract;

    /**
     * Изменяем иерархию объектов.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  Request  $request
     *
     * @return MoveResponseContract
     */
    public function move(ItemsServiceContract $itemsService, Request $request): MoveResponseContract;
}
