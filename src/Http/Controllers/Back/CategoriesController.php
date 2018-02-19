<?php

namespace InetStudio\Categories\Http\Controllers\Back;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Categories\Contracts\Events\ModifyCategoryEventContract;
use InetStudio\Meta\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;
use InetStudio\Categories\Contracts\Http\Controllers\Back\CategoriesControllerContract;

/**
 * Class CategoriesController.
 */
class CategoriesController extends Controller implements CategoriesControllerContract
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
     * @param SaveCategoryRequestContract $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveCategoryRequestContract $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование категории.
     *
     * @param null $id
     *
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
     * @param SaveCategoryRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveCategoryRequestContract $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение категории.
     *
     * @param SaveCategoryRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SaveCategoryRequestContract $request, $id = null): RedirectResponse
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
        $item->title = strip_tags($request->get('title'));
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

        event(app()->makeWith(ModifyCategoryEventContract::class, compact($item, $oldParent, $newParent)));

        Session::flash('success', 'Категория «'.$item->name.'» успешно '.$action);

        return response()->redirectToRoute('back.categories.edit', $item->fresh()->id);
    }

    /**
     * Удаление категории.
     *
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {
            $oldParent = $item->parent;

            event(app()->makeWith(ModifyCategoryEventContract::class, compact($item, $oldParent)));

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
}
