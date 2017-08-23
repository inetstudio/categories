<?php

namespace InetStudio\Categories\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Categories\Requests\SaveCategoryRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;

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

        return view('admin.module.categories::pages.categories.index', [
            'tree' => $tree,
        ]);
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
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {
            $tree = CategoryModel::getTree();

            return view('admin.module.categories::pages.categories.form', [
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
    public function update(SaveCategoryRequest $request, $id = null)
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
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {
            $action = 'отредактирована';
        } else {
            $action = 'создана';
            $item = new CategoryModel();
        }

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = strip_tags($request->input('description.text'));
        $item->content = $request->input('content.text');
        $item->save();

        $parentId = $request->get('parent_id');

        if ($parentId == 0) {
            $item->saveAsRoot();
        } else {
            $item->appendToNode(CategoryModel::find($parentId))->save();
        }

        $this->saveMeta($item, $request);
        $this->saveImages($item, $request, ['og_image', 'content']);

        Session::flash('success', 'Категория «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.categories.edit', $item->fresh()->id));
    }

    /**
     * Сохраняем мета теги.
     *
     * @param CategoryModel $item
     * @param SaveCategoryRequest $request
     */
    private function saveMeta($item, $request)
    {
        if ($request->has('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }
        }
    }

    /**
     * Сохраняем изображения.
     *
     * @param CategoryModel $item
     * @param SaveCategoryRequest $request
     * @param array $images
     */
    private function saveImages($item, $request, $images)
    {
        foreach ($images as $name) {
            $properties = $request->get($name);

            if (isset($properties['images'])) {
                $item->clearMediaCollectionExcept($name, $properties['images']);

                foreach ($properties['images'] as $image) {
                    if ($image['id']) {
                        $media = $item->media->find($image['id']);
                        $media->custom_properties = $image['properties'];
                        $media->save();
                    } else {
                        $filename = $image['filename'];

                        $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image['tempname'];

                        $media = $item->addMedia($file)
                            ->withCustomProperties($image['properties'])
                            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                            ->usingFileName($image['tempname'])
                            ->toMediaCollection($name, 'categories');
                    }

                    $item->update([
                        $name => str_replace($image['src'], '/img/' . $media->id, $item[$name]),
                    ]);
                }
            } else {
                if (isset($properties['tempname']) && isset($properties['filename'])) {
                    $image = $properties['tempname'];
                    $filename = $properties['filename'];

                    $item->clearMediaCollection($name);

                    array_forget($properties, ['tempname', 'temppath', 'filename']);
                    $properties = array_filter($properties);

                    $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image;

                    $item->addMedia($file)
                        ->withCustomProperties($properties)
                        ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                        ->usingFileName($image)
                        ->toMediaCollection($name, 'categories');
                } else {
                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);

                    if ($media) {
                        $media->custom_properties = $properties;
                        $media->save();
                    }
                }
            }
        }
    }

    /**
     * Удаление категории.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0 && $item = CategoryModel::find($id)) {

            //TODO добавить detach

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
        $data = [];

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
