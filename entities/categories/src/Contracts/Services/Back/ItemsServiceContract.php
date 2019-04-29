<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Services\Back;

use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;

/**
 * Interface ItemsServiceContract.
 */
interface ItemsServiceContract extends BaseServiceContract
{
    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param  int  $id
     *
     * @return CategoryModelContract
     */
    public function save(array $data, int $id): CategoryModelContract;

    /**
     * Удаляем модель.
     *
     * @param  mixed  $id
     *
     * @return bool|null
     */
    public function destroy($id): ?bool;

    /**
     * Получаем дерево объектов.
     *
     * @return array
     */
    public function getTree(): array;

    /**
     * Перестраиваем дерево объектов.
     *
     * @param  array  $data
     *
     * @return int
     */
    public function rebuildTree(array $data): int;

    /**
     * Присваиваем категории объекту.
     *
     * @param $categories
     *
     * @param $item
     */
    public function attachToObject($categories, $item): void;
}
