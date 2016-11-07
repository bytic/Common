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

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->getParameter('key');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setMid($value)
    {
        return $this->setParameter('mid', $value);
    }

    /**
     * @return mixed
     */
    public function getMid()
    {
        return $this->getParameter('mid');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }
}
