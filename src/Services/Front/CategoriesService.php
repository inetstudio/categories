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
     * @param array $properties
     * @param array $with
     *
     * @return mixed
     */
    public function getCategoryBySlug(string $slug, array $properties = [], array $with = [])
    {
        return $this->repository->getItemBySlug($slug, $properties, $with);
    }

    /**
     * Родительский объект.
     *
     * @param $category
     * @param array $properties
     * @param array $with
     *
     * @return mixed
     */
    public function getParentCategory($category, array $properties = [], array $with = [])
    {
        return $this->repository->getParentItem($category, $properties, $with);
    }

    /**
     * Подобъекты.
     *
     * @param $parentCategory
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getSubCategories($parentCategory, array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getSubItems($parentCategory, $properties, $with, $sort);
    }

    /**
     * Получаем информацию по объектам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems(['created_at', 'updated_at'], [], ['created_at' => 'desc']);

        $resource = app()->make('InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
