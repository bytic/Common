<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

/**
 * Class AbstractResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{

    /**
     * @param $name
     * @return mixed
     */
    public function getDataProperty($name)
    {
        return $this->data[$name];
    }
}
