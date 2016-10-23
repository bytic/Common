<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasModelRequest;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class ServerCompletePurchaseRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class ServerCompletePurchaseRequest extends AbstractRequest
{
    use NameWorksTrait;
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
