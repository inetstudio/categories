<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract;
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
     * PagesService constructor.
     *
     * @param CategoriesRepositoryContract $repository
     */
    public function __construct(CategoriesRepositoryContract $repository)
    {
        $this->repository = $repository;
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
        return $this->repository->getCategoryBySlug($slug, $returnBuilder);
    }

    /**
     * Родительский объект.
     *
     * @param CategoryModelContract $category
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getParentCategory(CategoryModelContract $category, bool $returnBuilder = false)
    {
        return $this->repository->getParentCategory($category, $returnBuilder);
    }

    /**
     * Подобъекты.
     *
     * @param CategoryModelContract $parentCategory
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getSubCategories(CategoryModelContract $parentCategory, bool $returnBuilder = false)
    {
        return $this->repository->getSubCategories($parentCategory, $returnBuilder);
    }

    /**
     * Получаем информацию по объектам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllCategories();

        $resource = app()->make('InetStudio\Categories\Contracts\Transformers\Front\CategoriesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
