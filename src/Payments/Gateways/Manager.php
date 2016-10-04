<?php

namespace ByTIC\Common\Payments\Gateways;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse;
use DirectoryIterator;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Utility\Traits\SingletonTrait;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

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
     * @return Gateway[]|null
     */
    public function getItems()
    {
        $this->checkItemsInit();

        return $this->items;
    }

    protected function checkItemsInit()
    {
        if ($this->items === null) {
            $this->initItems();
        }
    }

    protected function initItems()
    {
        $this->items = [];
        $iterator = new DirectoryIterator(dirname(__FILE__).'/Providers');
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                $name = $fileinfo->getFilename();
                if (!in_array($name, ['.', '..', 'AbstractGateway'])) {
                    $gateway = $this->newItem($name);
                    $this->addItem($gateway);
                }
            }
        }
    }

    /**
     * @param bool|string $name
     * @return Gateway
     */
    public function newItem($name = false)
    {
        $className = 'ByTIC\Common\Payments\Gateways\Providers\\'.$name.'\Gateway';
        /** @var Gateway $object */
        $object = new $className();
        $object->setManager($this);

        return $object;
    }

    /**
     * @param Gateway $gateway
     * @param null $name
     */
    public function addItem($gateway, $name = null)
    {
        $name = $name ? $name : $gateway->getName();
        $this->items[$name] = $gateway;
    }

    /**
     * @param $name
     * @return Gateway
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
     * @param RecordManager $modelManager
     * @param string $callback
     * @param null|HttpRequest $httpRequest
     * @return bool|\Omnipay\Common\Message\ResponseInterface
     */
    public function detectItemFromHttpRequest($modelManager, $callback = null, $httpRequest = null)
    {
        $this->checkItemsInit();
        $callback = $callback ? $callback : 'completePurchase';
        foreach ($this->items as $item) {
            if ($httpRequest) {
                $item->setHttpRequest($httpRequest);
            }
            if (method_exists($item, $callback)) {
                /** @var CompletePurchaseRequest $request */
                $request = $item->$callback(['modelManager' => $modelManager]);
                $response = $request->send();
                if (is_subclass_of($response, CompletePurchaseResponse::class)) {
                    return $response;
                }
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
        return translator()->translate('payment-gateways.labels.'.$type, $params, $language);
    }

    /**
     * @param $name
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function getMessage($name, $params = [], $language = false)
    {
        return translator()->translate('payment-gateways.messages.'.$name, $params, $language);
    }
}
