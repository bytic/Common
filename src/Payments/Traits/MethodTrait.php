<?php

namespace ByTIC\Common\Payments\Traits;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 */
trait MethodTrait
{

    /**
     * @return mixed
     */
    public abstract function getPaymentGatewayOptions();
}
