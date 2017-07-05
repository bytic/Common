<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse as AbstractResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\Traits\CompletePurchaseResponseTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class CompletePurchaseResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->hasModel() ? $this->getModel()->status == 'active' : false;
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isPending()
    {
        return $this->hasModel() ? $this->getModel()->status == 'pending' : parent::isPending();
    }
}
