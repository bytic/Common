<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class PurchaseRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class PurchaseRequest extends AbstractRequest
{
    use NameWorksTrait;

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setOrderId($value)
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * @return string
     */
    public function getConfirmUrl()
    {
        return $this->getParameter('confirmUrl');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setConfirmUrl($value)
    {
        return $this->setParameter('confirmUrl', $value);
    }

    /**
     * @param  array $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        $class = $this->getNamespacePath().'\PurchaseResponse';

        return $this->response = new $class($this, $data, $this->getEndpoint());
    }
}
