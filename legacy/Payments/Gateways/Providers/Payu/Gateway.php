<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseRequest;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\Providers\Payu
 *
 * @method PurchaseRequest purchaseFromModel($record)
 *
 */
class Gateway extends AbstractGateway
{

    /**
     * @param $value
     * @return mixed
     */
    public function setMerchant($value)
    {
        return $this->setParameter('merchant', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if ($this->getMerchant() && $this->getSecretKey()) {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getMerchant()
    {
        return $this->getParameter('merchant');
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }
}
