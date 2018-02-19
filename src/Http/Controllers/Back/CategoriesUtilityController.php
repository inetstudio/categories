<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use InetStudio\Categories\Models\CategoryModel;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesUtilityControllerContract;

/**
 * Class CategoriesUtilityController.
 */
class CategoriesUtilityController extends Controller implements CategoriesUtilityControllerContract
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

        $items = CategoryModel::select(['id', 'name', 'slug'])->where('name', 'LIKE', '%'.$search.'%')->get();

        if ($request->filled('type') and $request->get('type') == 'autocomplete') {
            $type = get_class(new CategoryModel());

            $data = $items->mapToGroups(function ($item) use ($type) {
                return [
                    'suggestions' => [
                        'value' => $item->name,
                        'data' => [
                            'id' => $item->id,
                            'type' => $type,
                            'title' => $item->name,
                            'path' => parse_url($item->href, PHP_URL_PATH),
                            'href' => $item->href,
                        ],
                    ],
                ];
            });
        } else {
            $data = $items->mapToGroups(function ($item) {
                return [
                    'items' => [
                        'id' => $item->id,
                        'name' => $item->name,
                    ],
                ];
            });
        }

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

        event(app()->makeWith('InetStudio\Categories\Contracts\Events\ModifyCategoryEventContract', []));

        return response()->json([
            'success' => true,
        ]);
    }
}
