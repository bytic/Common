<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Dispatcher;
use Nip\FrontController;
use Nip\Records\_Abstract\Row;
use Nip\Records\_Abstract\Table;
use Nip\Request;

/**
 * Class HasModels
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method string getName
 * @method Request getRequest
 * @method Dispatcher getDispatcher
 *
 * @method mixed call($action = false, $controller = false, $module = false, $params = array())
 */
trait HasModels
{
    protected $_model = null;
    protected $_modelManager = null;

    public function getModel()
    {
        if ($this->_model === null) {
            $this->initModel();
        }

        return $this->_model;
    }

    protected function initModel()
    {
        $name = str_replace(array("async-", "modal-"), '', $this->getName());

        return $this->stringToModelName($name);
    }

    protected function stringToModelName($name)
    {
        $class = inflector()->classify($name);
        $elements = explode("_", $class);
        $this->_model = implode("_", $elements);
    }

    /**
     * @return Table
     */
    protected function getModelManager()
    {
        if ($this->_modelManager == null) {
            $this->initModelManager();
        }

        return $this->_modelManager;
    }

    /**
     * @return Table
     */
    protected function initModelManager()
    {
        $this->_modelManager = $this->newModelManagerInstance($this->getModel());
    }

    /**
     * @param string $managerName
     * @return Table
     */
    protected function newModelManagerInstance($managerName)
    {
        return call_user_func(array($managerName, "instance"));
    }

    /**
     * @param bool $key
     * @return Row|null
     */
    protected function getModelFromRequest($key = false)
    {
        $requestKey = 'model-'.$this->getModelManager()->getTable();
        if ($this->getRequest()->attributes->has($requestKey) === false) {
            $this->initModelFromRequest($key);
        }

        return $this->getRequest()->attributes->get($requestKey);
    }

    protected function initModelFromRequest($key = false)
    {
        $item = $this->checkItem($this->getRequest(), $key);
        $requestKey = 'model-'.$this->getModelManager()->getTable();
        $this->getRequest()->attributes->set($requestKey, $item);
    }

    /**
     * @param string $name
     * @param bool|string $key
     * @return Row|null
     */
    protected function checkForeignModelFromRequest($name, $key = false)
    {
        $requestKey = 'model-'.$name;
        if ($this->getRequest()->attributes->has($requestKey) === false) {
            $this->initForeignModelFromRequest($name, $key);
        }
        return $this->getRequest()->attributes->get($requestKey);
    }

    /**
     * @param string $name
     * @return Row|null
     */
    protected function getForeignModelFromRequest($name)
    {
        $this->checkForeignModelFromRequest($name);
        $requestKey = 'model-'.$name;

        return $this->getRequest()->attributes->get($requestKey);
    }

    /**
     * @param string $name
     * @return Row|null
     */
    protected function hasForeignModelFromRequest($name)
    {
        $requestKey = 'model-'.$name;
        return $this->getRequest()->attributes->has($requestKey);
    }

    /**
     * @param $name
     * @param $key
     * @throws \Exception
     */
    protected function initForeignModelFromRequest($name, $key)
    {
        if ($key == false) {
            throw new \Exception('initForeignModelFromRequest needs a key parameter');
        }
        $this->call('getModelFromRequest', $name, false, array($key));
    }

    /**
     * @param Row $model
     * @return null|Row
     */
    protected function checkAndSetForeignModelInRequest($model)
    {
        $requestKey = 'model-'.$model->getManager()->getTable();
        if ($this->call('checkItemResult', $model->getManager()->getController(), false, array($model)) == true) {
            $this->getRequest()->attributes->set($requestKey, $model);
            return $model;
        }
        return null;
    }

    protected function checkItem($request = false, $key = false)
    {
        $item = $this->findItemFromRequest($request, $key);
        if ($this->checkItemResult($item)) {
            return $item;
        }

        return $this->dispatchNotFoundResponse();
    }

    protected function checkItemResult($item)
    {
        $manager = $this->getModelManager();
        $class = $manager->getModel();

        if ($item instanceof $class) {
            if ($this->checkItemAccess($item) === false) {
                $this->dispatchAccessDeniedResponse();
            } else {
                return true;
            }
        }

        return false;
    }

    protected function dispatchAccessDeniedResponse()
    {
        FrontController::instance()->getTrace()->add('Acces denied to item');
        $this->getDispatcher()->forward("index", "access");
    }

    protected function dispatchNotFoundResponse()
    {
        FrontController::instance()->getTrace()->add('No valid item [manager:'.get_class($this->getModelManager()).']');
        $this->getDispatcher()->forward("index", "error");
    }

    /**
     * alias for checkItem
     *
     * @param bool $request
     * @param bool $key
     * @return false|Row|void
     */
    protected function findItemOrFail($request = false, $key = false)
    {
        return $this->checkItem($request, $key);
    }

    protected function checkItemAccess($item)
    {
        return $item instanceof Row;
    }


    protected function findItemFromRequest($request = false, $key = false)
    {
        list($urlKey, $modelKey) = $this->getUrlModelKey($key);

        $manager = $this->getModelManager();
        $value = $this->getItemValueFromRequest($request, $urlKey);
        $params = array();
        $params['where'][] = array("`{$modelKey}` = ?", $value);

        return $manager->findOneByParams($params);
    }

    protected function getUrlModelKey($key = false)
    {
        $manager = $this->getModelManager();

        if (is_array($key)) {
            list($urlKey, $modelKey) = $key;
        } elseif (is_string($key)) {
            $urlKey = $key;
            $modelKey = $manager->getPrimaryKey();
        } else {
            $urlKey = $manager->getUrlPK();
            $modelKey = $manager->getPrimaryKey();
        }

        return array($urlKey, $modelKey);
    }

    protected function getItemValueFromRequest($request = false, $urlKey)
    {
        if (!$request) {
            $request = $this->getRequest();
        }

        if ($request instanceof Request) {
            $value = $request->get($urlKey);
        } else {
            $value = $request[$urlKey];
        }

        $value = clean($value);

        return $value;
    }
}