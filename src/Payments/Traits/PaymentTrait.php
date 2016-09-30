<?php

namespace ByTIC\Common\Payments\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\RedirectForm;
use ByTIC\Common\Payments\Methods\Traits\RecordTrait as PaymentMethod;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 */
trait PaymentTrait
{

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
        $gateway = $this->getPaymentMethod()->getGateway();
        return $gateway->getRedirectForm($this);
    }

    /**
     * @return PaymentMethod
     */
    abstract public function getPaymentMethod();
}
