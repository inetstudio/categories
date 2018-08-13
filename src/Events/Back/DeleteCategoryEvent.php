<?php

namespace InetStudio\Categories\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\Categories\Contracts\Events\Back\DeleteCategoryEventContract;

/**
 * Class DeleteCategoryEvent.
 */
class DeleteCategoryEvent implements DeleteCategoryEventContract
{
    use SerializesModels;

    /**
     * @var string
     */
    public $className;

    /**
     * @var int
     */
    public $id;

    /**
     * DeleteCategoryEvent constructor.
     *
     * @param string $className
     * @param int $id
     */
    public function __construct(string $className, int $id)
    {
        $this->className = $className;
        $this->id = $id;
    }
}
