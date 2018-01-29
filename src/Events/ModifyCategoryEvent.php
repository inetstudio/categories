<?php

namespace InetStudio\Categories\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class ModifyCategoryEvent
 * @package InetStudio\Categories\Events
 */
class ModifyCategoryEvent
{
    use SerializesModels;

    public $object;
    public $oldParent;
    public $newParent;

    /**
     * Create a new event instance.
     *
     * ModifyCategoryEvent constructor.
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
