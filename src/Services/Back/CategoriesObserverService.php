<?php

namespace InetStudio\Categories\Services\Back;

use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Repositories\CategoriesRepositoryContract;
use InetStudio\Categories\Contracts\Services\Back\CategoriesObserverServiceContract;

/**
 * Class CategoriesObserverService.
 */
class CategoriesObserverService implements CategoriesObserverServiceContract
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
     * Событие "объект создается".
     *
     * @param CategoryModelContract $item
     */
    public function creating(CategoryModelContract $item): void
    {
    }

    /**
     * Событие "объект создан".
     *
     * @param CategoryModelContract $item
     */
    public function created(CategoryModelContract $item): void
    {
    }

    /**
     * Событие "объект обновляется".
     *
     * @param CategoryModelContract $item
     */
    public function updating(CategoryModelContract $item): void
    {
    }

    /**
     * Событие "объект обновлен".
     *
     * @param CategoryModelContract $item
     */
    public function updated(CategoryModelContract $item): void
    {
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param CategoryModelContract $item
     */
    public function deleting(CategoryModelContract $item): void
    {
    }

    /**
     * Событие "объект удален".
     *
     * @param CategoryModelContract $item
     */
    public function deleted(CategoryModelContract $item): void
    {
    }
}
