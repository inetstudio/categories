<?php

namespace InetStudio\Categories\Listeners;

use Illuminate\Support\Facades\Cache;

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
            Cache::tags(['categories'])->forget('CategoriesService_getParentCategory_'.$object->id);
            Cache::tags(['categories'])->forget('CategoriesService_getCategoryBySlug_'.md5($object->slug));
        } else {
            Cache::tags(['categories'])->flush();
        }

        if ($oldParent) {
            Cache::tags(['categories'])->forget('CategoriesService_getSubCategories_'.$oldParent->id);
        }

        if ($newParent && $newParent->id != $oldParent->id) {
            Cache::tags(['categories'])->forget('CategoriesService_getSubCategories_'.$newParent->id);
        }

        Cache::tags(['materials'])->flush();
    }
}
