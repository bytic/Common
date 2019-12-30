<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Controllers\Traits\HasViewTrait;
use Nip\Request;
use Nip\View;

/**
 * Class HasView
 * @package ByTIC\Common\Controllers\Traits
 *
 * @deprecated Use \Nip\Controllers\Traits\HasViewTrait
 */
trait HasView
{
    use HasViewTrait;

    /**
     * @return View
     */
    protected function getViewObject()
    {
        return new \App_View();
    }
}

