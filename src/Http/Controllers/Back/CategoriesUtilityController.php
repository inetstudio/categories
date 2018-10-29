<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Categories\Contracts\Http\Responses\Back\Utility\MoveResponseContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\Categories\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract;

/**
 * Class CategoriesUtilityController.
 */
class CategoriesUtilityController extends Controller implements CategoriesUtilityControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services;

    /**
     * CategoriesUtilityController constructor.
     */
    public function __construct()
    {
        $this->services['categories'] = app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract');
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return SlugResponseContract
     */
    public function getSlug(Request $request): SlugResponseContract
    {
        $id = (int) $request->get('id');
        $name = $request->get('name');

        $model = $this->services['categories']->getCategoryObject($id);

        $slug = ($name) ? SlugService::createSlug($model, 'slug', $name) : '';

        return app()->makeWith(SlugResponseContract::class, [
            'slug' => $slug,
        ]);
    }

    /**
     * Возвращаем объекты для поля.
     *
     * @param Request $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(Request $request): SuggestionsResponseContract
    {
        $search = $request->get('q');
        $type = $request->get('type');

        $data = app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract')
            ->getSuggestions($search, $type);

        return app()->makeWith(SuggestionsResponseContract::class, [
            'suggestions' => $data,
        ]);
    }

    /**
     * Изменяем иерархию объектов.
     *
     * @param Request $request
     *
     * @return MoveResponseContract
     */
    public function move(Request $request): MoveResponseContract
    {
        $data = json_decode($request->get('data'), true);

        $result = app()->make('InetStudio\Categories\Services\Back\CategoriesService')
            ->rebuildTree($data);

        return app()->makeWith(MoveResponseContract::class, [
            'result' => $result,
        ]);
    }
}
