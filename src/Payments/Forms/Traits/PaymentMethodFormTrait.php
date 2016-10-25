<?php

namespace ByTIC\Common\Payments\Forms\Traits;

use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;

/**
 * Class PaymentMethodFormTrait
 * @package ByTIC\Common\Payments\Forms\Traits
 */
trait PaymentMethodFormTrait
{

    /**
     * @var null|GatewaysManager
     */
    protected $paymentGatewaysManager = null;

    /**
     * @var null|Gateway[]
     */
    protected $paymentGatewaysItems = null;

    /**
     * @var null|array
     */
    protected $paymentGatewaysNames = null;

    protected function initPaymentGatewaysOptionsForm()
    {
        $gateways = $this->getPaymentGatewaysItems();

        foreach ($gateways as $name => $gateway) {
            $gateway->getOptionsForm()->setForm($this)->init();
        }
    }

    /**
     * @return Gateway[]
     */
    public function getPaymentGatewaysItems()
    {
        $this->checkPaymentGatewaysValues();

        return $this->paymentGatewaysItems;
    }

    /**
     * @param null $paymentGatewaysItems
     */
    public function setPaymentGatewaysItems($paymentGatewaysItems)
    {
        $this->paymentGatewaysItems = $paymentGatewaysItems;
    }


    protected function checkPaymentGatewaysValues()
    {
        if ($this->paymentGatewaysItems == null) {
            $this->paymentGatewaysItems = $this->getPaymentGatewaysManager()->getItems();
            $this->paymentGatewaysNames = $this->getPaymentGatewaysManager()->getItemsName();
        }
    }


    /**
     * @return GatewaysManager
     */
    public function getPaymentGatewaysManager()
    {
        if ($this->paymentGatewaysManager == null) {
            $this->initPaymentGatewaysManager();
        }

        return $this->paymentGatewaysManager;
    }

    /**
     * @param null $paymentGatewaysManager
     */
    public function setPaymentGatewaysManager($paymentGatewaysManager)
    {
        $this->paymentGatewaysManager = $paymentGatewaysManager;
    }

    protected function initPaymentGatewaysManager()
    {
        $this->setPaymentGatewaysManager($this->newPaymentGatewaysManager());
    }

    /**
     * @return GatewaysManager
     */
    protected function newPaymentGatewaysManager()
    {
        return GatewaysManager::instance();
    }
}
