<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use InetStudio\Categories\Models\CategoryModel;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Categories\Events\ModifyCategoryEvent;

/**
 * Class CategoriesUtilityController
 * @package InetStudio\Categories\Http\Controllers\Back
 */
class CategoriesUtilityController extends Controller
{
    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $slug = ($name) ? SlugService::createSlug(CategoryModel::class, 'slug', $name) : '';

        return response()->json($slug);
    }

    /**
     * Возвращаем категории для поля.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $search = $request->get('q');
        $data = [];

        $data['items'] = CategoryModel::select(['id', 'name'])->where('name', 'LIKE', '%'.$search.'%')->get()->toArray();

        return response()->json($data);
    }

    /**
     * Изменяем иерархию категорий.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function move(Request $request): JsonResponse
    {
        $data = json_decode($request->get('data'), true);

        CategoryModel::defaultOrder()->rebuildTree($data);

        event(new ModifyCategoryEvent());

        return response()->json([
            'success' => true,
        ]);
    }
}
