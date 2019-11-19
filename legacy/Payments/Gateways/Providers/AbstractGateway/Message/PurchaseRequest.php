<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse\RedirectTrait;

/**
 * Class PurchaseRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 *
 * @method PurchaseResponse|RedirectTrait send()
 */
abstract class PurchaseRequest extends AbstractRequest
{

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
    public function getOrderName()
    {
        return $this->getParameter('orderName');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setOrderDate($value)
    {
        return $this->setParameter('orderDate', $value);
    }

    /**
     * @return string
     */
    public function getOrderDate()
    {
        return $this->getParameter('orderDate');
    }

    /**
     * @param  string $value
     * @return mixed
     */
    public function setOrderName($value)
    {
        return $this->setParameter('orderName', $value);
    }

    /**
     * @param  array $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        $class = $this->getResponseClass();

        return $this->response = new $class($this, $data, $this->getEndpoint());
    }

    /**
     * @return bool
     */
    protected function isProviderRequest()
    {
        return true;
    }
}
