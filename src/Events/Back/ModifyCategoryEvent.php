<?php

namespace InetStudio\Categories\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\Categories\Contracts\Events\Back\ModifyCategoryEventContract;

/**
 * Class ModifyCategoryEvent.
 */
class ModifyCategoryEvent implements ModifyCategoryEventContract
{
    use SerializesModels;

    public $object;
    public $oldParent;
    public $newParent;

    /**
     * Create a new event instance.
     *
     * ModifyCategoryEvent constructor.
     *
     * @param $object
     * @param $oldParent
     * @param $newParent
     */
    public function __construct($object = null, $oldParent = null, $newParent = null)
    {
        $this->object = $object;
        $this->oldParent = $oldParent;
        $this->newParent = $newParent;
    }
}
