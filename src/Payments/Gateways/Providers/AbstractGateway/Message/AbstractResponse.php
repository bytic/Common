<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

/**
 * Class AbstractResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{

    /**
     * @return mixed
     */
    public function getIpnData()
    {
        return $this->getDataProperty('ipn_data');
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getDataProperty($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getIpnDataItem($key)
    {
        return $this->getDataProperty('ipn_data')[$key];
    }
}
