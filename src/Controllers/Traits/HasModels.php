<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Records\_Abstract\Row;
use Nip\Records\_Abstract\Table;

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
        $name = str_replace(array("async-", "modal-"), '', $this->_name);
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
        $this->_modelManager = call_user_func(array($this->getModel(), "instance"));
    }

    /**
     * @return null|Row
     */
    protected function getItemFromRequest()
    {
        if ($this->getRequest()->attributes->has('item-crud') === false) {

        }
        return $this->getRequest()->attributes->has('item-crud');
    }


    protected function checkItem($request = false, $key = false)
    {
        $item = $this->findItemFromRequest($request, $key);
        if ($this->checkItemResult($item)) {
            $this->getRequest()->attributes->set('item-crud', $item);
            return $item;
        }

        return $this->checkItemError($item);
    }

    protected function checkItemResult($item)
    {
        $manager = $this->getModelManager();

        $class = $manager->getModel();
        if ($item instanceof $class) {
            if ($this->checkItemAccess($item)) {
                return true;
            }
        }
        return false;
    }

    protected function checkItemError($item)
    {
        \Nip\FrontController::instance()->getTrace()->add('No valid item');
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
        return true;
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

        if ($request instanceof \Nip\Request) {
            $value = $request->$urlKey;
        } else {
            $value = $request[$urlKey];
        }

        $value = clean($value);
        return $value;
    }

    public function getRequestKey()
    {
        return $this->getModel();
    }
}