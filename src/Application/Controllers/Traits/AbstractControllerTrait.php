<?php

namespace ByTIC\Common\Application\Controllers\Traits;

use ByTIC\Common\Records\Records;
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

    /**
     * @return Records
     */
    public abstract function getModelManager();

    /**
     * @param $url
     * @param null $code
     * @return mixed
     */
    abstract protected function redirect($url, $code = null);

    /**
     * @param $message
     * @param $url
     * @param string $type
     * @param bool $name
     * @return mixed
     */
    abstract protected function flashRedirect($message, $url, $type = 'success', $name = false);
}
