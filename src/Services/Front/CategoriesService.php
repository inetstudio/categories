<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract;

/**
 * Class CategoriesService.
 */
class CategoriesService implements CategoriesServiceContract
{
    /**
     * @var
     */
    public $repository;

    /**
     * CategoriesService constructor.
     */
    public function __construct()
    {
        $this->repository = app()->make('InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract');
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getCategoryBySlug(string $slug, array $params = [])
    {
        return $this->repository->getItemBySlug($slug, $params);
    }

    /**
     * Родительский объект.
     *
     * @param $category
     * @param array $params
     *
     * @return mixed
     */
    public function getParentCategory($category, array $params = [])
    {
        return $this->repository->getParentItem($category, $params);
    }

    /**
     * Подобъекты.
     *
     * @param $parentCategory
     * @param array $params
     *
     * @return mixed
     */
    public function getSubCategories($parentCategory, array $params = [])
    {
        return $this->repository->getSubItems($parentCategory, $params);
    }

    /**
     * Получаем информацию по объектам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems([
            'columns' => ['created_at', 'updated_at'],
            'order' => ['created_at' => 'desc'],
        ]);

        $resource = app()->make('InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
