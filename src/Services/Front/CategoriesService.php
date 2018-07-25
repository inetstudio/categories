<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
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
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getCategoryBySlug(string $slug, bool $returnBuilder = false)
    {
        return $this->repository->getItemBySlug($slug, $returnBuilder);
    }

    /**
     * Родительский объект.
     *
     * @param $category
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getParentCategory($category, bool $returnBuilder = false)
    {
        return $this->repository->getParentItem($category, $returnBuilder);
    }

    /**
     * Подобъекты.
     *
     * @param $parentCategory
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getSubCategories($parentCategory, bool $returnBuilder = false)
    {
        return $this->repository->getSubItems($parentCategory, $returnBuilder);
    }

    /**
     * Получаем информацию по объектам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems();

        $resource = app()->make('InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
