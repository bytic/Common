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
    abstract public function getResponse();

    /**
     * @return Request
     */
    abstract public function getRequest();

    /**
     * @return View
     */
    abstract public function getView();

    /**
     * @return string
     */
    abstract public function getAction();

    /**
     * @return Records
     */
    abstract public function getModelManager();

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
