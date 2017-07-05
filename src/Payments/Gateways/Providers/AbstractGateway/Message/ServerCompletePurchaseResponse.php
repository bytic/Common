<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasModelProcessedResponse;

/**
 * Class ServerCompletePurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class ServerCompletePurchaseResponse extends AbstractResponse
{
    use HasModelProcessedResponse;

    public function send()
    {
        echo $this->getContent();
        die();
    }

    /**
     * @return string
     */
    abstract public function getContent();
}
