<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Application\Modules\Frontend\Controllers\Traits\ErrorControllerTrait as FrontendErrorControllerTrait;

/**
 * Class ErrorControllerTrait
 *
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait ErrorControllerTrait
{
    use AbstractControllerTrait;
    use FrontendErrorControllerTrait;


    /**
     * @return string
     */
    public function getLayout()
    {
        return 'error';
    }
}
