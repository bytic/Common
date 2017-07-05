<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\Async\Files;
use ByTIC\Common\Controllers\Traits\Async\Gallery;
use ByTIC\Common\Controllers\Traits\Async\Images;
use ByTIC\Common\Controllers\Traits\Async\Logos;
use ByTIC\Common\Controllers\Traits\Async\Models;
use ByTIC\Common\Controllers\Traits\Async\ResponseTrait;

/**
 * Class AbstractAsyncControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractAsyncControllerTrait
{
    use ResponseTrait;
    use Images;
    use Gallery;
    use Files;
    use Logos;
    use Models;
}
