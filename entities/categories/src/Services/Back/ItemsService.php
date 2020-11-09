<?php

namespace InetStudio\CategoriesPackage\Categories\Services\Back;

use Illuminate\Support\Arr;
use League\Fractal\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use InetStudio\AdminPanel\Base\Services\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * ItemsService constructor.
     *
     * @param  CategoryModelContract  $model
     */
    public function __construct(CategoryModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param  int  $id
     *
     * @return CategoryModelContract
     *
     * @throws BindingResolutionException
     */
    public function save(array $data, int $id): CategoryModelContract
    {
        $action = ($id) ? 'отредактирована' : 'создана';

        $itemData = Arr::only($data, $this->model->getFillable());
        $item = $this->saveModel($itemData, $id);

        $oldParent = $item->parent;

        $parentId = Arr::get($data, 'parent_id', 0);

        if ($parentId == 0) {
            $item->saveAsRoot();
        } else {
            $item->appendToNode($this->getItemById($parentId))->save();
        }

        $newParent = $item->parent;

        $metaData = Arr::get($data, 'meta', []);
        app()->make('InetStudio\MetaPackage\Meta\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($metaData, $item);

        $images = (config('categories.images.conversions.category')) ? array_keys(
            config('categories.images.conversions.category')
        ) : [];
        app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract')
            ->attachToObject(request(), $item, $images, 'categories', 'category');

        event(
            app()->makeWith(
                'InetStudio\CategoriesPackage\Categories\Contracts\Events\Back\ModifyItemEventContract',
                [
                    'item' => $item,
                    'oldParent' => $oldParent,
                    'newParent' => $newParent,
                ]
            )
        );

        Session::flash('success', 'Категория «'.$item->name.'» успешно '.$action);

        return $item;
    }

    /**
     * Удаляем модель.
     *
     * @param  mixed  $id
     *
     * @return bool|null
     *
     * @throws BindingResolutionException
     */
    public function destroy($id): ?bool
    {
        $result = parent::destroy($id);

        if ($result) {
            $item = $this->model::withTrashed()->find($id);

            event(
                app()->make(
                    'InetStudio\CategoriesPackage\Categories\Contracts\Events\Back\ModifyItemEventContract',
                    [
                        'item' => $item,
                        'oldParent' => $item->parent,
                        'newParent' => null,
                    ]
                )
            );
        }

        return $result;
    }

    /**
     * Получаем дерево объектов.
     *
     * @return array
     *
     * @throws BindingResolutionException
     */
    public function getTree(): array
    {
        $tree = $this->model::defaultOrder()->get()->toTree();

        $transformer = app()->make(
            'InetStudio\CategoriesPackage\Categories\Contracts\Transformers\Back\TreeTransformerContract'
        );
        $serializer = app()->make('InetStudio\AdminPanel\Base\Contracts\Serializers\SimpleDataArraySerializerContract');

        $resource = $transformer->transformCollection($tree);

        $manager = new Manager();
        $manager->setSerializer($serializer);

        $transformation = $manager->createData($resource)->toArray();

        return $transformation;
    }

    /**
     * Перестраиваем дерево объектов.
     *
     * @param  array  $data
     *
     * @return int
     */
    public function rebuildTree(array $data): int
    {
        $result = $this->model::defaultOrder()->rebuildTree($data);

        event(
            app()->makeWith(
                'InetStudio\CategoriesPackage\Categories\Contracts\Events\Back\ModifyItemEventContract',
                []
            )
        );

        return $result;
    }

    /**
     * Присваиваем категории объекту.
     *
     * @param $categories
     * @param array $data
     *
     * @param $item
     */
    public function attachToObject($categories, $item, array $data = []): void
    {
        if ($categories instanceof Request) {
            $categories = $categories->get('categories', []);
        } elseif (is_string($categories)) {
            $categories = explode(',', $categories);
        } else {
            $categories = (array) $categories;
        }

        if (! empty($categories)) {
            $item->syncCategories($this->model::whereIn('id', $categories)->get(), $data);
        } else {
            $item->detachCategories($item->categories);
        }
    }
}
