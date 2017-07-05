<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

/**
 * Class AbstractResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractResponse extends \Omnipay\Common\Message\AbstractResponse
{

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->getDataProperty('valid') === true;
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
     * @return mixed
     */
    public function getIpnData()
    {
        return $this->getDataProperty('ipn_data');
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
