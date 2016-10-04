<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\ServerCompleteResponse;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\Providers\Payu
 *
 * @method Payu getProviderClass
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
     * @return ServerCompleteResponse
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
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if ($this->getOption('merchant') && $this->getOption('secretKey')) {
            return true;
        }

        return false;
    }

    public function detectIPNResponse()
    {
        return $this->detectRequestFields($_POST, ['HASH', 'REFNOEXT']);
    }

    public function parseIPNResponse()
    {
//
//        $donation = Donations::instance()->findOne($_POST["REFNOEXT"]);
//        if ($donation) {
//            $method = $donation->getMethod();
//            $this->setOptions($method->getOptions('payu'));
//            $this->setPaymentMethodModel($method);
//        }

        /* Internet Payment Notification */
        $gateway = $this->getProviderClass();
        $result = "";                /* string for compute HASH for received data */
        $return = "";                /* string to compute HASH for return result */
        $signature = $_POST["HASH"];    /* HASH received */
        $body = "";



        /* read info received */
        ob_start();
        while (list($key, $val) = each($_POST)) {
            $$key = $val;

            /* get values */
            if ($key != "HASH") {

                if (is_array($val)) {
                    $result .= ArrayExpand($val);
                } else {
                    $size = strlen(StripSlashes($val));
                    $result .= $size . StripSlashes($val);
                }

            }

        }

        $body = ob_get_contents();
        ob_end_flush();


        $date_return = date("YmdGis");

        $return = strlen($_POST["IPN_PID"][0]) . $_POST["IPN_PID"][0] . strlen($_POST["IPN_PNAME"][0]) . $_POST["IPN_PNAME"][0];
        $return .= strlen($_POST["IPN_DATE"]) . $_POST["IPN_DATE"] . strlen($date_return) . $date_return;

        $hash = $gateway->hmac($result); /* HASH for data received */

        $body .= $result . "\r\n\r\nHash: " . $hash . "\r\n\r\nSignature: " . $signature . "\r\n\r\nReturnSTR: " . $return;

        if ($hash == $signature) {
            echo "Verified OK!";

            if ($donation) {
                $donation->received = date(DATE_DB);
                $donation->setStatus('active');

                /* ePayment response */
                $result_hash = $gateway->hmac($return);
                echo "<EPAYMENT>" . $date_return . "|" . $result_hash . "</EPAYMENT>";
            } else {
                echo 'error donation';
            }
        } else {
            /* warning email */
            mail("webmaster@gecad.ro", "BAD IPN Signature", $body, "");
        }
    }
}
