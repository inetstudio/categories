<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\SaveResponseContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\IndexResponseContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Categories\DestroyResponseContract;

/**
 * Class CategoriesController.
 */
class CategoriesController extends Controller implements CategoriesControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    private $services;

    /**
     * PagesController constructor.
     */
    public function __construct()
    {
        $this->services['categories'] = app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract');
    }

    /**
     * Список объектов.
     *
     * @return IndexResponseContract
     */
    public function index(): IndexResponseContract
    {
        $tree = $this->services['categories']->getTree();

        return app()->makeWith('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\IndexResponseContract', [
            'data' => compact('tree'),
        ]);
    }

    /**
     * Добавление объекта.
     *
     * @return FormResponseContract
     */
    public function create(): FormResponseContract
    {
        $item = $this->services['categories']->getCategoryObject();
        $tree = $this->services['categories']->getTree();

        return app()->makeWith('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract', [
            'data' => compact('item', 'tree'),
        ]);
    }

    /**
     * Создание объекта.
     *
     * @param SaveCategoryRequestContract $request
     *
     * @return SaveResponseContract
     */
    public function store(SaveCategoryRequestContract $request): SaveResponseContract
    {
        return $this->save($request);
    }

    /**
     * Редактирование объекта.
     *
     * @param int $id
     *
     * @return FormResponseContract
     */
    public function edit($id = 0): FormResponseContract
    {
        $item = $this->services['categories']->getCategoryObject($id);
        $tree = $this->services['categories']->getTree();

        return app()->makeWith('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\FormResponseContract', [
            'data' => compact('item', 'tree'),
        ]);
    }

    /**
     * Обновление объекта.
     *
     * @param SaveCategoryRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    public function update(SaveCategoryRequestContract $request, int $id = 0): SaveResponseContract
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение объекта.
     *
     * @param SaveCategoryRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    private function save(SaveCategoryRequestContract $request, int $id = 0): SaveResponseContract
    {
        $item = $this->services['categories']->save($request, $id);

        return app()->makeWith('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\SaveResponseContract', [
            'item' => $item,
        ]);
    }

    /**
     * Удаление объекта.
     *
     * @param int $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(int $id = 0): DestroyResponseContract
    {
        $result = $this->services['categories']->destroy($id);

        return app()->makeWith('InetStudio\Categories\Contracts\Http\Responses\Back\Categories\DestroyResponseContract', [
            'result' => ($result === null) ? false : $result,
        ]);
    }
}
