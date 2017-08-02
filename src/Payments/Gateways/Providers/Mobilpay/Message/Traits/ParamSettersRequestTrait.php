<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\Traits;

/**
 * Class ParamSettersRequestTrait
 * @package ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\Traits
 */
trait ParamSettersRequestTrait
{
    /**
     * @param $value
     * @return mixed
     */
    public function setSandbox($value)
    {
        return $this->setParameter('sandbox', $value);
    }

    /**
     * @return mixed
     */
    public function getSandbox()
    {
        return $this->getParameter('sandbox');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setSignature($value)
    {
        return $this->setParameter('signature', $value);
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->getParameter('signature');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setCertificate($value)
    {
        return $this->setParameter('certificate', $value);
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->getParameter('certificate');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }
}
