<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\Async\Models;
use ByTIC\Common\Controllers\Traits\Async\ResponseTrait;
use ByTIC\MediaLibraryModule\Application\Modules\AbstractModule\Controllers\Traits\HasMediaAsyncTrait;

/**
 * Class AbstractAsyncControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractAsyncControllerTrait
{
    use HasMediaAsyncTrait;
    use ResponseTrait;
    use Models;
}
