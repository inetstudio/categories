<?php

namespace InetStudio\Categories\Listeners;

use Illuminate\Support\Facades\Cache;

/**
 * Class ClearCategoryCacheListener
 * @package InetStudio\Categories\Listeners
 */
class ClearCategoryCacheListener
{
    /**
     * ClearCategoryCacheListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        $object = $event->object;
        $oldParent = $event->oldParent;
        $newParent = $event->newParent;

        if ($object) {
            Cache::forget('CategoriesService_getParentCategory_'.md5($object->id));
            Cache::forget('CategoriesService_getCategoryBySlug_'.md5($object->slug));
        } else {
            Cache::flush();
        }

        if ($oldParent) {
            Cache::forget('CategoriesService_getSubCategories_'.md5($oldParent->id));
        }

        if ($newParent) {
            Cache::forget('CategoriesService_getSubCategories_'.md5($newParent->id));
        }

        Cache::tags(['materials'])->flush();
    }
}
