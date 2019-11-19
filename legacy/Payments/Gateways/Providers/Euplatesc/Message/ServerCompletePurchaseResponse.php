<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse as AbstractResponse;
use ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits\CompletePurchaseResponseTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponse extends AbstractResponse
{
    use CompletePurchaseResponseTrait;

    /**
     * @return string
     */
    public function getContent()
    {
        return 'OK';
    }
}
