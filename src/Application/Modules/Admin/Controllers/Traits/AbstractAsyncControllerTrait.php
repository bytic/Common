<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\MediaLibraryModule\Application\Modules\AbstractModule\Controllers\Traits\HasMediaAsyncTrait;

/**
 * Class AbstractAsyncControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractAsyncControllerTrait
{
    use HasMediaAsyncTrait;
    use \ByTIC\Controllers\Behaviors\Async\ResponseTrait;
    use \ByTIC\Controllers\Behaviors\Async\Models;
}
