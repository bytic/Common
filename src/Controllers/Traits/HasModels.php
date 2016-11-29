<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Controller;
use Nip\Dispatcher\Dispatcher;
use Nip\Logger\Exception;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;
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

    protected $model = null;

    protected $modelManager = null;

    /**
     * @param bool $key
     * @return Record|null
     */
    protected function getModelFromRequest($key = false)
    {
        $requestKey = 'model-'.$this->getModelManager()->getTable();
        if ($this->getRequest()->attributes->has($requestKey) === false) {
            $this->initModelFromRequest($key);
        }

        return $this->getRequest()->attributes->get($requestKey);
    }

    /**
     * @return RecordManager
     */
    protected function getModelManager()
    {
        if ($this->modelManager == null) {
            $this->initModelManager();
        }

        return $this->modelManager;
    }

    /**
     * @throws Exception
     */
    protected function initModelManager()
    {
        $managerClass = $this->getModel();
        $modelManager = $this->newModelManagerInstance($this->getModel());
        if ($modelManager instanceof RecordManager) {
            $this->modelManager = $this->newModelManagerInstance($this->getModel());
        } else {
            throw new Exception("invalid ModelManager name [$managerClass] for controller [" . $this->getClassName() . "]");
        }
    }

    /**
     * @return null|string
     */
    public function getModel()
    {
        if ($this->model === null) {
            $this->initModel();
        }

        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    protected function initModel()
    {
        $this->setModel($this->generateModelName());
    }

    /**
     * @return string
     */
    protected function generateModelName()
    {
        $name = str_replace(["async-", "modal-"], '', $this->getName());
        $class = inflector()->classify($name);
        $elements = explode("_", $class);
        if ($this->isNamespaced()) {
            $model = $this->getModelNamespace();
            $elements[] = end($elements);
            $model .= implode('\\', $elements);
        } else {
            $model = implode("_", $elements);
        }

        return $model;
    }

    /**
     * @return bool
     */
    abstract public function isNamespaced();

    /**
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
     * @param string $managerName
     * @return RecordManager
     */
    protected function newModelManagerInstance($managerName)
    {
        return call_user_func([$managerName, "instance"]);
    }

    /**
     * @return string
     */
    abstract public function getClassName();

    /**
     * @param bool $key
     */
    protected function initModelFromRequest($key = false)
    {
        $item = $this->checkItem($this->getRequest(), $key);
        $this->setModelFromRequest($item);
    }

    /**
     * @param bool $request
     * @param bool $key
     * @return null|Record
     */
    protected function checkItem($request = false, $key = false)
    {
        $item = $this->findItemFromRequest($request, $key);
        if ($this->checkItemResult($item)) {
            return $item;
        }

        $this->dispatchNotFoundResponse();

        return null;
    }

    /**
     * @param bool $request
     * @param bool $key
     * @return false|Record
     */
    protected function findItemFromRequest($request = false, $key = false)
    {
        list($urlKey, $modelKey) = $this->getUrlModelKey($key);

        $manager = $this->getModelManager();
        $params = [];
        if (is_array($modelKey)) {
            foreach ($modelKey as $i => $field) {
                $urlKeyField = $urlKey[$i];
                $value = $this->getItemValueFromRequest($request, $urlKeyField);
                $params['where'][] = ["`{$field}` = ?", $value];
            }
        } else {
            $value = $this->getItemValueFromRequest($request, $urlKey);
            $params['where'][] = ["`{$modelKey}` = ?", $value];
        }

        return $manager->findOneByParams($params);
    }

    /**
     * @param bool $key
     * @return array
     */
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

        return [$urlKey, $modelKey];
    }

    /**
     * @param bool $request
     * @param $urlKey
     * @return mixed|string
     */
    protected function getItemValueFromRequest($request = false, $urlKey = null)
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

    /**
     * @param $item
     * @return bool
     */
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

    /**
     * @param $item
     * @return bool
     */
    protected function checkItemAccess($item)
    {
        return $item instanceof Record;
    }

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

    /**
     * @param $item
     */
    protected function setModelFromRequest($item)
    {
        $requestKey = $this->getRequestKeyFromController($this);
        $this->getRequest()->attributes->set($requestKey, $item);
    }

    /**
     * @param Controller|HasModels $controller
     * @return string
     */
    protected function getRequestKeyFromController($controller)
    {
        return $this->getRequestKeyFromString($controller->getModelManager()->getTable());
    }

    /**
     * @param $name
     * @return string
     */
    protected function getRequestKeyFromString($name)
    {
        return 'model-'.$name;
    }

    /**
     * @param string $name
     * @return Record|null
     */
    protected function getForeignModelFromRequest($name)
    {
        $this->checkForeignModelFromRequest($name);
        $requestKey = $this->getRequestKeyFromString($name);

        return $this->getRequest()->attributes->get($requestKey);
    }

    /**
     * @param string $name
     * @param bool|string $key
     * @return Record|null
     */
    protected function checkForeignModelFromRequest($name, $key = false)
    {
        $requestKey = $this->getRequestKeyFromString($name);
        if ($this->getRequest()->attributes->has($requestKey) === false) {
            $this->initForeignModelFromRequest($name, $key);
        }

        return $this->getRequest()->attributes->get($requestKey);
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
        $model = $this->call('getModelFromRequest', $name, false, [$key]);
        $requestKey = $this->getRequestKeyFromString($name);
        $this->getRequest()->attributes->set($requestKey, $model);
    }

    /**
     * @param string $name
     * @return bool|null
     */
    protected function hasForeignModelFromRequest($name)
    {
        $requestKey = $this->getRequestKeyFromString($name);

        return $this->getRequest()->attributes->has($requestKey);
    }

    /**
     * @param Record $model
     * @return null|Record
     */
    protected function checkAndSetForeignModelInRequest($model)
    {
        $requestKey = $this->getRequestKeyFromModel($model);
        if ($this->call('checkItemResult', $model->getManager()->getController(), false, [$model]) == true) {
            $this->getRequest()->attributes->set($requestKey, $model);

            return $model;
        }

        return null;
    }

    /**
     * @param Record $model
     * @return string
     */
    protected function getRequestKeyFromModel($model)
    {
        return $this->getRequestKeyFromString($model->getManager()->getTable());
    }

    /**
     * alias for checkItem
     *
     * @param bool $request
     * @param bool $key
     * @return false|Record
     */
    protected function findItemOrFail($request = false, $key = false)
    {
        return $this->checkItem($request, $key);
    }
}
