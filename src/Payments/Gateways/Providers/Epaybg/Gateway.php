<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Epaybg;

class Gateway extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway
{

    protected $label = 'ePayBg';

    public function isActive()
    {
        if ($this->options['min'] && $this->options['secret']) {
            return true;
        }
        return false;
    }

    public function generatePaymentForm($donation)
    {
        $pClass = $this->getProviderClass();

        $pClass->setData('invoice', $donation->id);
        $pClass->setData('amount', $donation->amount);
        $pClass->setData('description', iconv("UTF-8", "ASCII//TRANSLIT", $donation->getFullName()));

        $donor = $donation->getOrgDonor();
//        $pClass->setData('bill', array(
//            'fname'	   => $donor->first_name,   // nume
//            'lname'	   => $donor->last_name,   // prenume
//            'country'  => 'Romania',   // tara
//            'email'	   => $donor->email,   // email
//        ));

        $pClass->returnUrl = $donation->getConfirmURL();
        $pClass->cancelUrl = $donation->getProject()->getOrganization()->getURL();

        return $pClass->generateForm();
    }

    public function detectConfirmResponse()
    {
        if ($_GET['id']) {
            $donation = Donations::instance()->findOne($_GET['id']);
            if ($donation) {
                $gateway = $donation->getPayment_Method()->getType()->getGateway();
                if ($gateway->getName() == $this->getName()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function detectIPNResponse()
    {
        return $this->detectRequestFields($_POST, array('encoded', 'checksum'));
    }

    public function parseConfirmResponse()
    {
        $donation = Donations::instance()->findOne($_GET['id']);
        if ($donation) {
            $gateway = $donation->getPayment_Method()->getType()->getGateway();
        }
        return $donation;
    }

    public function parseIPNResponse()
    {
        $encoded = $_POST['encoded'];
        $checksum = $_POST['checksum'];

        $organization = Organizations::instance()->getCurrent();

        $paymentMethods = $organization->getPayment_Methods();
        foreach ($paymentMethods as $method) {
            if ($method->getOption('payment_gateway') == $this->getName()) {
                $this->setOptions($method->getOptions($this->getName()));
            }
        }

        $pClass = $this->getProviderClass();
        $hmac = $pClass->_hmac('sha1', $encoded); # XXX SHA-1 algorithm REQUIRED

        if ($hmac == $checksum) { # XXX Check if the received CHECKSUM is OK
            $data = base64_decode($encoded);
            $lines_arr = split("\n", $data);
            $info_data = '';

            foreach ($lines_arr as $line) {
                if (preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/",
                    $line, $regs)) {
                    $invoice = $regs[1];
                    $status = $regs[2];
                    $pay_date = $regs[4]; # XXX if PAID
                    $stan = $regs[5]; # XXX if PAID
                    $bcode = $regs[6]; # XXX if PAID

                    # XXX process $invoice, $status, $pay_date, $stan, $bcode here
                    $donation = Donations::instance()->findOne($invoice);
                    if ($donation) {
                        if ($status === 'PAID') {
                            # XXX if OK for this invoice
                            $info_data .= "INVOICE=$invoice:STATUS=OK\n";

                            $donation->received = date(DATE_DB);
                            $donation->setStatus('active');
                        } else {
                            if ($status === 'DENIED') {
                                // log that the payment was denied
                            } else {
                                if ($status === 'EXPIRED') {
                                    // log that the payment expired
                                }
                            }
                        }
                    } else {
                        # XXX if not recognise this invoice
                        $info_data .= "INVOICE=$invoice:STATUS=NO\n";
                    }


                    # XXX if error for this invoice
                    # XXX $info_data .= "INVOICE=$invoice:STATUS=ERR\n";

                }
            }

            echo $info_data, "\n";
        } else {
            echo "ERR=Not valid CHECKSUM\n"; # XXX The description of error is REQUIRED
        }
        return $donation;
    }

    public function initProviderClass()
    {
        $class = new Epaybg();
        $class->setSecretKey($this->options['secret'])
            ->setMIN($this->options['min']);
        $class->setSandboxMode($this->options['sandbox'] == 'yes');
        $class->paymentpage = $this->options['paymentpage'];
        return $class;
    }
}