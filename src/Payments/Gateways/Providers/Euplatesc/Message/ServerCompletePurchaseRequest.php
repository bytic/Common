<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseRequest as AbstractRequest;
use ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits\CompletePurchaseRequestTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseRequest extends AbstractRequest
{
    use CompletePurchaseRequestTrait;
}
