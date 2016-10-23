<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

/**
 * Class ServerCompletePurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class ServerCompletePurchaseResponse extends AbstractResponse
{

    /**
     * @return $this
     */
    abstract public function processModel();

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
