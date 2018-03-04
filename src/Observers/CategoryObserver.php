<?php

namespace InetStudio\Categories\Observers;

use InetStudio\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\Categories\Contracts\Observers\CategoryObserverContract;

class CategoryObserver implements CategoryObserverContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    protected $services;

    /**
     * CategoryObserver constructor.
     */
    public function __construct()
    {
        $this->services['categoriesObserver'] = app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesObserverServiceContract');
    }

    /**
     * Событие "объект создается".
     *
     * @param CategoryModelContract $item
     */
    public function creating(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->creating($item);
    }

    /**
     * Событие "объект создан".
     *
     * @param CategoryModelContract $item
     */
    public function created(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->created($item);
    }

    /**
     * Событие "объект обновляется".
     *
     * @param CategoryModelContract $item
     */
    public function updating(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->updating($item);
    }

    /**
     * Событие "объект обновлен".
     *
     * @param CategoryModelContract $item
     */
    public function updated(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->updated($item);
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param CategoryModelContract $item
     */
    public function deleting(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->deleting($item);
    }

    /**
     * Событие "объект удален".
     *
     * @param CategoryModelContract $item
     */
    public function deleted(CategoryModelContract $item): void
    {
        $this->services['categoriesObserver']->deleted($item);
    }
}
