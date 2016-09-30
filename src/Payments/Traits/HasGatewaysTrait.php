<?php

namespace ByTIC\Common\Payments\Traits;

use ByTIC\Common\Payments\Gateways\Manager;

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
     * @return Manager|null
     */
    public function getGatewaysManager()
    {
        if ($this->gatewaysManager == null) {
            $this->initGatewaysManager();
        }

        return $this->gatewaysManager;
    }

    protected function initGatewaysManager()
    {
        $this->gatewaysManager = new Manager();
    }

    protected function initGatewaysManager()
    {
        $this->gatewaysManager = new Manager();
    }

    /**
     * @param Manager|null $gatewaysManager
     */
    public function setGatewaysManager($gatewaysManager)
    {
        $this->gatewaysManager = $gatewaysManager;
    }

}