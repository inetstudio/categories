<?php

namespace InetStudio\CategoriesPackage\Categories\Contracts\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use InetStudio\AdminPanel\Base\Contracts\Models\BaseModelContract;

/**
 * Interface CategoryModelContract.
 */
interface CategoryModelContract extends BaseModelContract, Auditable, HasMedia
{
}
