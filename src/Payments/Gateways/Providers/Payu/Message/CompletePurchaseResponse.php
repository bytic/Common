<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse as AbstractResponse;

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
        return $this->data['valid'] === true;
    }
}
