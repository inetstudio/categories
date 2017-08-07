<?php

namespace InetStudio\Categories\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use InetStudio\Categories\Models\CategoryModel;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Categories\Requests\SaveCategoryRequest;

/**
 * Контроллер для управления категориями.
 *
 * Class ContestByTagStatusesController
 */
class CategoriesController extends Controller
{
    /**
     * Список категорий.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tree = CategoryModel::getTree();

        return view('admin.module.categories::pages.categories.index', ['tree' => $tree]);
    }

    /**
     * Добавление категории.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $tree = CategoryModel::getTree();

        return view('admin.module.categories::pages.categories.form', [
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
    public function store(SaveCategoryRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование категории.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = CategoryModel::where('id', '=', $id)->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        } else {
            $tree = CategoryModel::getTree();

            return view('admin.module.categories::pages.categories.form', [
                'item' => $item,
                'tree' => $tree,
            ]);
        }
    }

    /**
     * Обновление категории.
     *
     * @param SaveCategoryRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveCategoryRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение категории.
     *
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $edit = true;
            $item = CategoryModel::where('id', '=', $id)->first();

            if (empty($item)) {
                abort(404);
            }
        } else {
            $edit = false;
            $item = new CategoryModel();
        }

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = strip_tags($request->get('description'));
        $item->content = $request->get('content');
        $item->save();

        $parentId = $request->get('parent_id');

        if ($parentId == 0) {
            $item->saveAsRoot();
        } else {
            $item->appendToNode(CategoryModel::where('id', '=', $parentId)->first())->save();
        }

        if ($request->has('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }
        }

        foreach (['og_image'] as $name) {
            $properties = $request->get($name);

            if (isset($properties['base64'])) {
                $image = $properties['base64'];
                $filename = $properties['filename'];

                array_forget($properties, 'base64');
                array_forget($properties, 'filename');
            }

            if (isset($image) && isset($filename)) {
                if (isset($properties['type']) && $properties['type'] == 'single') {
                    $item->clearMediaCollection($name);
                    array_forget($properties, 'type');
                }

                $properties = array_filter($properties);

                $item->addMediaFromBase64($image)
                    ->withCustomProperties($properties)
                    ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                    ->usingFileName(md5($image).'.'.pathinfo($filename, PATHINFO_EXTENSION))
                    ->toMediaCollection($name, 'categories');
            } else {
                if (isset($properties['type']) && $properties['type'] == 'single') {
                    array_forget($properties, 'type');

                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);
                    $media->custom_properties = $properties;
                    $media->save();
                }
            }
        }

        $action = ($edit) ? 'отредактирована' : 'создана';
        Session::flash('success', 'Категория «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.categories.edit', $item->fresh()->id));
    }

    /**
     * Удаление категории.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = CategoryModel::where('id', '=', $id)->first();
        } else {
            return response()->json([
                'success' => false,
            ]);
        }

        if (empty($item)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request)
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
    public function getSuggestions(Request $request)
    {
        $search = $request->get('q');
        $data['items'] = CategoryModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();

        return response()->json($data);
    }

    public function move(Request $request)
    {
        $data = json_decode($request->get('data'), true);

        CategoryModel::defaultOrder()->rebuildTree($data);

        return response()->json([
            'success' => true,
        ]);
    }
}