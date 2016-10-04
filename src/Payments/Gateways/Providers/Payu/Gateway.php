<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\ServerCompletePurchaseResponse;

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
     * @param array $parameters
     * @return PurchaseResponse
     */
    public function purchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return CompletePurchaseResponse
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('CompletePurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return ServerCompletePurchaseResponse
     */
    public function serverCompletePurchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('ServerCompletePurchaseRequest', $parameters);
    }

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

//    public function parseIPNResponse()
//    {
//        echo "Verified OK!";
//
//        if ($donation) {
//            $donation->received = date(DATE_DB);
//            $donation->setStatus('active');
//
//            /* ePayment response */
//            $result_hash = $gateway->hmac($return);
//            echo "<EPAYMENT>" . $date_return . "|" . $result_hash . "</EPAYMENT>";
//        } else {
//            echo 'error donation';
//        }
//    }
}
