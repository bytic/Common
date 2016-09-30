<?php

namespace ByTIC\Common\Payments\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\RedirectForm;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 */
trait PaymentTrait
{

    /**
     * @return mixed
     */
    public function getPaymentGatewayOptions()
    {
        $gatewayName = $this->getOption('payment_gateway');
        return $this->getOptions($gatewayName);
    }

    /**
     * @return mixed
     */
    public function generateGatewayRedirectForm()
    {
        return $this->getGatewayRedirectForm()->generate();
    }

    /**
     * @return RedirectForm
     */
    public function getGatewayRedirectForm()
    {
        $gateway = $this->getGateway();
        return $gateway->getRedirectForm($this);
    }

    /**
     * @return Gateway
     */
    abstract public function getGateway();

}
