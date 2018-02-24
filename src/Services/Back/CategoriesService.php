<?php

namespace InetStudio\Categories\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\Session;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;
use InetStudio\Categories\Contracts\Repositories\Back\CategoriesRepositoryContract;

/**
 * Class CategoriesService.
 */
class CategoriesService implements CategoriesServiceContract
{
    /**
     * @var CategoriesRepositoryContract
     */
    private $repository;

    /**
     * CategoriesService constructor.
     *
     * @param CategoriesRepositoryContract $repository
     */
    public function __construct(CategoriesRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Получаем объект модели.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getCategoryObject(int $id = 0)
    {
        return $this->repository->getItemByID($id);
    }

    /**
     * Получаем объекты по списку id.
     *
     * @param array|int $ids
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getCategoriesByIDs($ids, bool $returnBuilder = false)
    {
        return $this->repository->getItemsByIDs($ids, $returnBuilder);
    }

    /**
     * Сохраняем модель.
     *
     * @param SaveCategoryRequestContract $request
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function save(SaveCategoryRequestContract $request, int $id): CategoryModelContract
    {
        $action = ($id) ? 'отредактирована' : 'создана';
        $item = $this->repository->save($request, $id);

        $oldParent = $item->parent;

        $parentId = $request->get('parent_id');

        if ($parentId == 0) {
            $item->saveAsRoot();
        } else {
            $item->appendToNode($this->repository->getItemByID($parentId))->save();
        }

        $newParent = $item->parent;

        app()->make('InetStudio\Meta\Contracts\Services\Back\MetaServiceContract')
            ->attachToObject($request, $item);

        $images = (config('categories.images.conversions')) ? array_keys(config('categories.images.conversions')) : [];
        app()->make('InetStudio\AdminPanel\Contracts\Services\Back\Images\ImagesServiceContract')
            ->attachToObject($request, $item, $images, 'categories');

        event(app()->makeWith('InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract', [
            'object' => $item,
            'oldParent' => $oldParent,
            'newParent' => $newParent,
        ]));

        Session::flash('success', 'Категория «'.$item->name.'» успешно '.$action);

        return $item;
    }

    /**
     * Удаляем модель.
     *
     * @param $id
     *
     * @return bool
     */
    public function destroy(int $id): ?bool
    {
        $item = $this->repository->getItemByID($id);

        $oldParent = $item->parent;

        event(app()->makeWith('InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract', [
            'object' => $item,
            'oldParent' => $oldParent,
        ]));

        return $this->repository->destroy($id);
    }

    /**
     * Получаем подсказки.
     *
     * @param string $search
     * @param $type
     *
     * @return array
     */
    public function getSuggestions(string $search, $type): array
    {
        $items = $this->repository->searchItemsByField('name', $search);

        $resource = (app()->makeWith('InetStudio\Categories\Contracts\Transformers\Back\SuggestionTransformerContract', [
            'type' => $type,
        ]))->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        if ($type && $type == 'autocomplete') {
            $data['suggestions'] = $transformation['data'];
        } else {
            $data['items'] = $transformation['data'];
        }

        return $data;
    }

    /**
     * Получаем дерево объектов.
     *
     * @return array
     */
    public function getTree(): array
    {
        $tree = $this->repository->getTree();

        $resource = (app()->makeWith('InetStudio\Categories\Contracts\Transformers\Back\TreeTransformerContract'))
            ->transformCollection($tree);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }

    /**
     * Перестраиваем дерево объектов.
     *
     * @param array $data
     *
     * @return int
     */
    public function rebuildTree(array $data): int
    {
        $result = $this->repository->rebuildTree($data);

        event(app()->makeWith('InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract', []));

        return $result;
    }

    /**
     * Присваиваем категории объекту.
     *
     * @param $request
     *
     * @param $item
     */
    public function attachToObject($request, $item)
    {
        if ($request->filled('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize($this->repository->getItemsByIDs($categories));
        } else {
            $item->uncategorize($item->categories);
        }
    }
}
