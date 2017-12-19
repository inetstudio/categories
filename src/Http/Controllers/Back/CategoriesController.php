<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Categories\Events\ModifyCategoryEvent;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Categories\Http\Requests\Back\SaveCategoryRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;

/**
 * Контроллер для управления категориями.
 *
 * Class ContestByTagStatusesController
 */
class CategoriesController extends Controller
{
    use MetaManipulationsTrait;
    use ImagesManipulationsTrait;

    /**
     * Список категорий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $tree = CategoryModel::getTree();

        return view('admin.module.categories::back.pages.index', [
            'tree' => $tree,
        ]);
    }

    /**
     * Добавление категории.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $tree = CategoryModel::getTree();

        return view('admin.module.categories::back.pages.form', [
            'item' => new CategoryModel(),
            'tree' => $tree,
        ]);
    }

    /**
     * Создание категории.
     *
     * @param SaveCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveCategoryRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование категории.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {
            $tree = CategoryModel::getTree();

            return view('admin.module.categories::back.pages.form', [
                'item' => $item,
                'tree' => $tree,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление категории.
     *
     * @param SaveCategoryRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveCategoryRequest $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение категории.
     *
     * @param SaveCategoryRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SaveCategoryRequest $request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {
            $action = 'отредактирована';
        } else {
            $action = 'создана';
            $item = new CategoryModel();
        }

        $oldParent = $item->parent;

        $item->name = strip_tags($request->get('name'));
        $item->slug = strip_tags($request->get('slug'));
        $item->h1 = strip_tags($request->get('h1'));
        $item->description = strip_tags($request->input('description.text'));
        $item->content = $request->input('content.text');
        $item->save();

        $parentId = $request->get('parent_id');

        if ($parentId == 0) {
            $item->saveAsRoot();
        } else {
            $item->appendToNode(CategoryModel::find($parentId))->save();
        }

        $newParent = $item->parent;

        $this->saveMeta($item, $request);
        $this->saveImages($item, $request, ['og_image', 'content'], 'categories');

        event(new ModifyCategoryEvent($item, $oldParent, $newParent));

        Session::flash('success', 'Категория «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.categories.edit', $item->fresh()->id);
    }

    /**
     * Удаление категории.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {

            //TODO добавить detach

            event(new ModifyCategoryEvent($item, $item->parent));

            $item->delete();

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $slug = SlugService::createSlug(CategoryModel::class, 'slug', $name);

        return response()->json($slug);
    }

    /**
     * Возвращаем категории для поля.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $search = $request->get('q');
        $data = [];

        $data['items'] = CategoryModel::select(['id', 'name'])->where('name', 'LIKE', '%'.$search.'%')->get()->toArray();

        return response()->json($data);
    }

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
