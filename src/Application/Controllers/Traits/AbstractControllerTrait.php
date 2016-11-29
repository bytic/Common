<?php

namespace ByTIC\Common\Application\Controllers\Traits;

use Nip\Http\Response\Response;
use Nip\Request;
use Nip\View;

/**
 * Class AbstractControllerTrait
 * @package ByTIC\Common\Application\Controllers\Traits
 */
trait AbstractControllerTrait
{

    /**
     * @return Response
     */
    public abstract function getResponse();

    /**
     * @return Request
     */
    public abstract function getRequest();

    /**
     * @return View
     */
    public abstract function getView();

    /**
     * @return string
     */
    public abstract function getAction();
}
