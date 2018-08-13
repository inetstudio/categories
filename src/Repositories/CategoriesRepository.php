<?php

namespace InetStudio\Categories\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract;
use InetStudio\Categories\Contracts\Http\Requests\Back\SaveCategoryRequestContract;

/**
 * Class CategoriesRepository.
 */
class CategoriesRepository implements CategoriesRepositoryContract
{
    /**
     * @var CategoryModelContract
     */
    public $model;

    /**
     * CategoriesRepository constructor.
     *
     * @param CategoryModelContract $model
     */
    public function __construct(CategoryModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * Получаем модель репозитория.
     *
     * @return CategoryModelContract
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Возвращаем пустой объект по id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getEmptyObjectById(int $id)
    {
        return $this->model::select(['id'])->where('id', '=', $id)->first();
    }

    /**
     * Возвращаем объект по id, либо создаем новый.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getItemByID(int $id): CategoryModelContract
    {
        return $this->model::find($id) ?? new $this->model;
    }

    /**
     * Возвращаем удаленный объект по id, либо пустой.
     *
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function getTrashedItemByID(int $id): CategoryModelContract
    {
        return $this->model::onlyTrashed()->find($id) ?? new $this->model;
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery()
            ->whereIn('id', (array) $ids);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param SaveCategoryRequestContract $request
     * @param int $id
     *
     * @return CategoryModelContract
     */
    public function save(SaveCategoryRequestContract $request, int $id): CategoryModelContract
    {
        $item = $this->getItemByID($id);

        $item->name = strip_tags($request->get('name'));
        $item->slug = strip_tags($request->get('slug'));
        $item->title = strip_tags($request->get('title'));
        $item->description = $request->input('description.text');
        $item->content = $request->input('content.text');
        $item->save();

        return $item;
    }

    /**
     * Удаляем объект.
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy($id): ?bool
    {
        return $this->getItemByID($id)->delete();
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
        return $this->model::defaultOrder()->rebuildTree($data);
    }

    /**
     * Получаем дерево объектов.
     *
     * @return Collection
     */
    public function getTree(): Collection
    {
        return $this->model::defaultOrder()->get()->toTree();
    }

    /**
     * Ищем объекты.
     *
     * @param array $conditions
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function searchItems(array $conditions, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery([])->where($conditions);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllItems(bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['created_at', 'updated_at'])
            ->orderBy('created_at', 'desc');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['parent_id', 'title', 'description', 'content'], ['meta', 'media'])
            ->whereSlug($slug);

        if ($returnBuilder) {
            return $builder;
        }

        $item = $builder->first();

        return $item;
    }

    /**
     * Родительский объект.
     *
     * @param $item
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getParentItem($item, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['parent_id', 'title', 'description', 'content'], ['meta', 'media'])
            ->where('id', $item['parent_id']);

        if ($returnBuilder) {
            return $builder;
        }

        $item = $builder->first();

        return $item;
    }

    /**
     * Подобъекты.
     *
     * @param $parentItem
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getSubItems($parentItem, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['title'])
            ->defaultOrder()
            ->withDepth()
            ->having('depth', '=', 1);

        if ($returnBuilder) {
            $builder = $builder->where('parent_id', $parentItem['id']);

            return $builder;
        }

        return $builder->descendantsOf($parentItem['id']);
    }

    /**
     * Возвращаем запрос на получение объектов.
     *
     * @param array $extColumns
     * @param array $with
     *
     * @return Builder
     */
    protected function getItemsQuery($extColumns = [], $with = []): Builder
    {
        $defaultColumns = ['id', 'name', 'slug', 'created_at'];

        $relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            },
        ];

        return $this->model::select(array_merge($defaultColumns, $extColumns))
            ->with(array_intersect_key($relations, array_flip($with)));
    }
}
