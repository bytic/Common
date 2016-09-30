<?php

namespace ByTIC\Common\Payments\Gateways;

use Nip\Utility\Traits\SingletonTrait;

/**
 * Class Payment_Gateways
 */
class Manager
{

    use SingletonTrait;

    protected $_items = [];

    /**
     * @return bool|Payment_Gateway_Abstract
     */
    public function detectConfirmResponse()
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->detectConfirmResponse()) {
                return $item;
            }
        }
        return false;
    }

    public function getItems()
    {
        if (!$this->_items) {
            $iterator = new DirectoryIterator(dirname(__FILE__) . '/gateways');
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isDir()) {
                    $name = $fileinfo->getFilename();
                    if (!in_array($name, array('.', '..', 'abstract'))) {
                        $object = $this->newItem($name);
                        $this->_items[$name] = $object;
                    }
                }
            }
        }
        return $this->_items;
    }

    public function newItem($type = false)
    {
        $parts = explode("_", $type);
        $parts = array_map([inflector(), "camelize"], $parts);

        $className = 'Payment_Gateway_' . implode("_", $parts);
        $object = new $className();
        return $object;
    }

    public function detectIPNResponse()
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->detectIPNResponse()) {
                return $item;
            }
        }

        return false;
    }

    public function getItem($type = false)
    {
        if (!count($this->_items)) {
            $this->getItems();
        }
        if ($this->_items[$type]) {
            return $this->_items[$type];
        }

        return false;
    }

    public function getLabel($type, $params = array(), $language = false)
    {
        return translator()->translate('payment-gateways.labels.' . $type, $params, $language);
    }

    public function getMessage($name, $params = array(), $language = false)
    {
        return translator()->translate('payment-gateways.messages.' . $name, $params, $language);
    }
}