<?php

namespace InetStudio\Categories\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\AdminPanel\Services\Front\BaseService;
use InetStudio\AdminPanel\Services\Front\Traits\SlugsServiceTrait;
use InetStudio\Categories\Contracts\Services\Front\CategoriesServiceContract;

/**
 * Class CategoriesService.
 */
class CategoriesService extends BaseService implements CategoriesServiceContract
{
    use SlugsServiceTrait;

    /**
     * CategoriesService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract'));
    }

    /**
     * Родительский объект.
     *
     * @param $category
     * @param array $params
     *
     * @return mixed
     */
    public function getParentItem($category, array $params = [])
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
    public function getSubItems($parentCategory, array $params = [])
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
