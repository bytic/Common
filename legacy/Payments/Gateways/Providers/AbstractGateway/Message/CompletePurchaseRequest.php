<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasModelRequest;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class CompletePurchaseRequest extends AbstractRequest
{
    use HasModelRequest;

    public function initData()
    {
        parent::initData();
        $this->populateDataFromRequest();
    }

    protected function populateDataFromRequest()
    {
        $this->pushData('ipn_data', $this->httpRequest->request->all());
    }
}
