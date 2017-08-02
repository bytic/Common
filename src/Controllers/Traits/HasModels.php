<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\Models\HasModelFinder;
use ByTIC\Common\Controllers\Traits\Models\HasModelManagerTrait;
use Nip\Dispatcher\Dispatcher;
use Nip\Request;

/**
 * Class HasModels
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method string getName
 * @method Request getRequest
 * @method Dispatcher getDispatcher
 *
 * @method mixed call($action = false, $controller = false, $module = false, $params = [])
 */
trait HasModels
{
    use HasModelManagerTrait;
    use HasModelFinder;

    /**
     * Get Model namespace
     *
     * @return string
     */
    public function getModelNamespace()
    {
        return $this->getRootNamespace() . 'Models\\';
    }

    /**
     * @return string
     */
    abstract public function getRootNamespace();

    /**
     * Is namespaced controller
     *
     * @return bool
     */
    abstract public function isNamespaced();

    /**
     * @return string
     */
    abstract public function getClassName();

    protected function dispatchAccessDeniedResponse()
    {
        //        ->getTrace()->add('Acces denied to item');
        $this->getDispatcher()->forward("index", "access");
    }

    protected function dispatchNotFoundResponse()
    {
        //        ->getTrace()->add(
//            'No valid item [manager:'.get_class($this->getModelManager()).']'
//        );
        $this->getDispatcher()->forward("index", "error");
    }
}
