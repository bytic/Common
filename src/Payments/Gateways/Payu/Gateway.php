<?php

namespace ByTIC\Common\Payments\Gateways\Payu;

use ByTIC\Common\Payments\Gateways\AbstractGateway\Gateway as AbstractGateway;

class Gateway extends AbstractGateway
{

    public function isActive()
    {
        if ($this->_options['merchant'] && $this->_options['secretKey']) {
            return true;
        }
        return false;
    }

    public function generatePaymentForm($donation)
    {
        $pClass = $this->getProcesingClass();

        $pClass->setOrderRef($donation->id);
        $pClass->setOrderDate($donation->created);

        $PName = [];                                        //products name array
        $PName[] = $donation->getCCName();
        $pClass->setOrderPName($PName);

        $PCode = [];                                        //products code array
        $PCode[] = $donation->id;
        $pClass->setOrderPCode($PCode);

        $PPrice = [];
        $PPrice[] = $donation->amount;
        $pClass->setOrderPrice($PPrice);

        $PQTY = [];                                        //products qty array
        $PQTY[] = 1;
        $pClass->setOrderQTY($PQTY);

        $PVAT = [];                                        //products vat array
        $PVAT[] = 0;
        $pClass->setOrderVAT($PVAT);

        $orgDonor = $donation->getOrgDonor();
        $billing = array(
            "billFName" => $orgDonor->first_name,
            "billLName" => $orgDonor->last_name,
            "billCIIssuer" => '',
            "billCNP" => '',
            "billCompany" => '',
            "billFiscalCode" => '',
            "billRegNumber" => '',
            "billBank" => '',
            "billBankAccount" => '',
            "billPhone" => '-',
            "billEmail" => $orgDonor->email,
            "billCountryCode" => 'RO'
        );

        $pClass->setBilling($billing);

        $returnURL = $donation->getConfirmURL();
        $pClass->setBackRef($donation->getConfirmURL());

        $donation->status_notes = $pClass->hmac(strlen($returnURL) . $returnURL);
        $donation->update();

        return $pClass->generateForm();
    }

    public function detectConfirmResponse()
    {
        return $this->detectRequestFields($_GET, array('id', 'ctrl'));
    }

    public function detectIPNResponse()
    {
        return $this->detectRequestFields($_POST, array('HASH', 'REFNOEXT'));
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
            $this->setModel($method);
        }

        /* Internet Payment Notification */
        $gateway = $this->getProcesingClass();
        $result = "";                /* string for compute HASH for received data */
        $return = "";                /* string to compute HASH for return result */
        $signature = $_POST["HASH"];    /* HASH received */
        $body = "";

        function ArrayExpand($array)
        {
            $retval = "";
            for ($i = 0; $i < sizeof($array); $i++) {
                $size = strlen(StripSlashes($array[$i]));
                $retval .= $size . StripSlashes($array[$i]);
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

    public function initProcesingClass()
    {
        $class = new Payu();
        $class->secretKey = html_entity_decode($this->_options['secretKey']);
        $class->merchant = html_entity_decode($this->_options['merchant']);
//        $class->setTestMode(true);
        return $class;
    }
}