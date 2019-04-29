<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Http\Controllers\Back;

use InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\ItemsServiceContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Requests\Back\SaveItemRequestContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\SaveResponseContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\FormResponseContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\IndexResponseContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Http\Responses\Back\Resource\DestroyResponseContract;

/**
 * Interface ResourceControllerContract.
 */
interface ResourceControllerContract
{
    /**
     * Список объектов.
     *
     * @param  ItemsServiceContract  $resourceService
     *
     * @return IndexResponseContract
     */
    public function index(ItemsServiceContract $resourceService): IndexResponseContract;

    /**
     * Создание объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     *
     * @return FormResponseContract
     */
    public function create(ItemsServiceContract $resourceService): FormResponseContract;

    /**
     * Создание объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  SaveItemRequestContract  $request
     *
     * @return SaveResponseContract
     */
    public function store(ItemsServiceContract $resourceService, SaveItemRequestContract $request): SaveResponseContract;

    /**
     * Редактирование объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  int  $id
     *
     * @return FormResponseContract
     */
    public function edit(ItemsServiceContract $resourceService, int $id = 0): FormResponseContract;

    /**
     * Обновление объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  SaveItemRequestContract  $request
     * @param  int  $id
     *
     * @return SaveResponseContract
     */
    public function update(
        ItemsServiceContract $resourceService,
        SaveItemRequestContract $request,
        int $id = 0
    ): SaveResponseContract;

    /**
     * Удаление объекта.
     *
     * @param  ItemsServiceContract  $resourceService
     * @param  int  $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(ItemsServiceContract $resourceService, int $id = 0): DestroyResponseContract;
}
