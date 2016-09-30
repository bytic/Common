<?php

namespace ByTIC\Common\Payments\Gateways;

use ByTIC\Common\Payments\Gateways\AbstractGateway\Gateway;
use DirectoryIterator;
use Nip\Utility\Traits\SingletonTrait;

/**
 * Class Payment_Gateways
 */
class Manager
{

    use SingletonTrait;

    /**
     * @var null|Gateway[]
     */
    protected $items = null;

    /**
     * @return bool|Gateway
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

    /**
     * @return AbstractGateway\Gateway[]|null
     */
    public function getItems()
    {
        $this->checkItemsInit();
        return $this->items;
    }

    protected function checkItemsInit()
    {
        if (!$this->items === null) {
            $this->initItems();
        }
    }

    protected function initItems()
    {
        $this->items = [];
        $iterator = new DirectoryIterator(dirname(__FILE__) . '/Providers');
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                $name = $fileinfo->getFilename();
                if (!in_array($name, ['.', '..', 'AbstractGateway'])) {
                    $gateway = $this->newItem($name);
                    $this->addItem($gateway, $name);
                }
            }
        }
    }

    /**
     * @param bool|string $type
     * @return Gateway
     */
    public function newItem($type = false)
    {
        $parts = explode("_", $type);
        $parts = array_map([inflector(), "camelize"], $parts);

        $className = 'Payment_Gateway_' . implode("_", $parts);
        $object = new $className();
        return $object;
    }

    /**
     * @param Gateway $gateway
     * @param null $name
     */
    public function addItem($gateway, $name = null)
    {
        $this->items[$name] = $gateway;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        $this->checkItemsInit();
        if ($this->items[$name]) {
            return $this->items[$name];
        }
        return null;
    }

    /**
     * @return bool|Gateway
     */
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

    /**
     * @param $type
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function getLabel($type, $params = [], $language = false)
    {
        return translator()->translate('payment-gateways.labels.' . $type, $params, $language);
    }

    /**
     * @param $name
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function getMessage($name, $params = [], $language = false)
    {
        return translator()->translate('payment-gateways.messages.' . $name, $params, $language);
    }
}
