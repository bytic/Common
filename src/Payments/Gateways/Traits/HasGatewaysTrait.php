<?php

namespace ByTIC\Common\Payments\Gateways\Traits;

use ByTIC\Common\Payments\Gateways\Manager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;

/**
 * Class HasGatewaysTrait
 * @package ByTIC\Common\Payments\Traits
 */
trait HasGatewaysTrait
{

    /**
     * @var null|Manager
     */
    protected $gatewaysManager = null;

    /**
     * @var null|Gateway
     */
    protected $gateway = null;

    /**
     * @return Gateway|null
     */
    public function getGateway()
    {
        if ($this->gateway === null) {
            $this->initGateway();
        }
        return $this->gateway;
    }

    /**
     * @param Gateway|null $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    protected function initGateway()
    {
        $gateway = $this->newGateway($this->getGatewayName());
        $this->setGateway($gateway);
    }

    /**
     * @param $name
     * @return null|Gateway
     */
    protected function newGateway($name)
    {
        $gateway = $this->getGatewaysManager()->get($name);
        return $gateway;
    }

    /**
     * @return Manager|null
     */
    public function getGatewaysManager()
    {
        if ($this->gatewaysManager == null) {
            $this->initGatewaysManager();
        }

        return $this->gatewaysManager;
    }

    /**
     * @param Manager|null $gatewaysManager
     */
    public function setGatewaysManager($gatewaysManager)
    {
        $this->gatewaysManager = $gatewaysManager;
    }

    protected function initGatewaysManager()
    {
        $this->setGatewaysManager(new Manager());
    }

    /**
     * @return string
     */
    abstract public function getGatewayName();

    /**
     * @return mixed
     */
    abstract protected function getGatewayOptions();
}
