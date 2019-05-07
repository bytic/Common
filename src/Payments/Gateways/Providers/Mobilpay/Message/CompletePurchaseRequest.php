<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseRequest as AbstractRequest;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function initData()
    {
        parent::initData();

        $this->validate('modelManager');

        $this->pushData('valid', false);
        if ($this->validateModel()) {
            $this->pushData('valid', true);
        }
    }

    /**
     * @return int
     */
    public function getModelIdFromRequest()
    {
        return $this->getHttpRequest()->query->get('orderId');
    }

    /**
     * @return mixed
     */
    protected function isProviderRequest()
    {
        return $this->hasGet('orderId');
    }

    /**
     * @return mixed
     */
    public function isValidNotification()
    {
        return $this->hasGet('orderId');
    }
}
