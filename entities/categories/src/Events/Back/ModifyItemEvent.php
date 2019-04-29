<?php

namespace InetStudio\CategoriesPackage\Categories\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\CategoriesPackage\Categories\Contracts\Models\CategoryModelContract;
use InetStudio\CategoriesPackage\Categories\Contracts\Events\Back\ModifyItemEventContract;

/**
 * Class ModifyItemEvent.
 */
class ModifyItemEvent implements ModifyItemEventContract
{
    use SerializesModels;

    /**
     * @var CategoryModelContract
     */
    public $item;

    /**
     * @var CategoryModelContract
     */
    public $oldParent;

    /**
     * @var CategoryModelContract
     */
    public $newParent;

    /**
     * Create a new event instance.
     *
     * ModifyItemEvent constructor.
     *
     * @param CategoryModelContract $item
     * @param CategoryModelContract $oldParent
     * @param CategoryModelContract $newParent
     */
    public function __construct(CategoryModelContract $item = null,
        CategoryModelContract $oldParent = null,
        CategoryModelContract $newParent = null)
    {
        $this->item = $item;
        $this->oldParent = $oldParent;
        $this->newParent = $newParent;
    }
}
