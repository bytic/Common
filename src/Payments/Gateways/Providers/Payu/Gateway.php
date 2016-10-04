<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;

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

    public function completePurchase(array $parameters = [])
    {
        return $this->createNamepacedRequest('CompletePurchaseRequest', $parameters);
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

    public function parseConfirmResponse()
    {
        $donation = Donations::instance()->findOne($_GET['id']);
        if ($donation) {
            if ($_GET['ctrl'] && $_GET['ctrl'] == $donation->status_notes) {
//                $donation->status = 'active';
//                $donation->received = date(DATE_DB);
//                $donation->save();
            } else {
                $donation->gateway_error = 'Eroare autorizare plata';
            }
        } else {
            $donation->gateway_error = 'Eroare autorizare plata';
        }

        return $donation;
    }

    public function parseIPNResponse()
    {
        ini_set("mbstring.func_overload", 0);
        if (ini_get("mbstring.func_overload") > 2) {  /* check if mbstring.func_overload is still set to overload strings(2)*/
            echo "WARNING: mbstring.func_overload is set to overload strings and might cause problems\n";
        }


        $donation = Donations::instance()->findOne($_POST["REFNOEXT"]);
        if ($donation) {
            $method = $donation->getMethod();
            $this->setOptions($method->getOptions('payu'));
            $this->setPaymentMethodModel($method);
        }

        /* Internet Payment Notification */
        $gateway = $this->getProviderClass();
        $result = "";                /* string for compute HASH for received data */
        $return = "";                /* string to compute HASH for return result */
        $signature = $_POST["HASH"];    /* HASH received */
        $body = "";

        function ArrayExpand($array)
        {
            $retval = "";
            for ($i = 0; $i < sizeof($array); $i++) {
                $size = strlen(StripSlashes($array[$i]));
                $retval .= $size.StripSlashes($array[$i]);
            }

            return $retval;
        }

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
                    $result .= $size.StripSlashes($val);
                }

            }

        }

        $body = ob_get_contents();
        ob_end_flush();


        $date_return = date("YmdGis");

        $return = strlen($_POST["IPN_PID"][0]).$_POST["IPN_PID"][0].strlen($_POST["IPN_PNAME"][0]).$_POST["IPN_PNAME"][0];
        $return .= strlen($_POST["IPN_DATE"]).$_POST["IPN_DATE"].strlen($date_return).$date_return;

        $hash = $gateway->hmac($result); /* HASH for data received */

        $body .= $result."\r\n\r\nHash: ".$hash."\r\n\r\nSignature: ".$signature."\r\n\r\nReturnSTR: ".$return;

        if ($hash == $signature) {
            echo "Verified OK!";

            if ($donation) {
                $donation->received = date(DATE_DB);
                $donation->setStatus('active');

                /* ePayment response */
                $result_hash = $gateway->hmac($return);
                echo "<EPAYMENT>".$date_return."|".$result_hash."</EPAYMENT>";
            } else {
                echo 'error donation';
            }
        } else {
            /* warning email */
            mail("webmaster@gecad.ro", "BAD IPN Signature", $body, "");
        }
    }

    /**
     * @return Payu
     */
    public function generateProviderClass()
    {
        $class = new Payu();
        $class->secretKey = html_entity_decode($this->getOption('secretKey'));
        $class->merchant = html_entity_decode($this->getOption('merchant'));

//        $class->setTestMode(true);
        return $class;
    }
}
