<?php

namespace ByTIC\Common\Payments\Traits;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 */
trait IsPurchasableTrait
{

    /**
     * @return array
     */
    public function getPurchaseParameters()
    {
        $parameters = [];
        $parameters['amount'] = $this->getPurchaseAmount();
        $parameters['currency'] = $this->getPurchaseCurrency();
        $parameters['orderId'] = $this->id;

        $parameters['confirmUrl'] = $this->getConfirmURL();
        $parameters['returnUrl'] = $this->getIpnURL();

        return $parameters;
    }

    abstract public function getPurchaseAmount();

    /**
     * @return string
     */
    public function getPurchaseCurrency()
    {
        return 'RON';
    }
}
