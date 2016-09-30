<?php

namespace ByTIC\Common\Payments\Methods\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Methods\Types\AbstractType;
use ByTIC\Common\Payments\Methods\Types\CreditCards;
use ByTIC\Common\Records\Traits\HasTypes\RecordTrait as HasTypesRecordTrait;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Methods\Traits
 *
 * @method AbstractType|CreditCards getType
 */
trait RecordTrait
{
    use HasTypesRecordTrait;

    /**
     * @return bool|Gateway|null
     */
    public function getGateway()
    {
        if ($this->getType()->getName() == 'credit-cards') {
            return $this->getType()->getGateway();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPaymentGatewayOptions()
    {
        $gatewayName = $this->getOption('payment_gateway');

        return $this->getOption($gatewayName);
    }

    /**
     * @param $name
     * @return mixed
     */
    public abstract function getOption($name);

    /**
     * @return mixed
     */
    public abstract function getOptions();
}
