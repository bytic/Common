<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Romcard;

class Gateway extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway
{

    /**
     * @return bool
     */
    public function isActive()
    {
        if ($this->options['MERCH_NAME'] && $this->options['TERMINAL'] && $this->options['KEY']) {
            return true;
        }
        return false;
    }

    /**
     * @param $donation
     * @return mixed
     */
    public function generatePaymentForm($donation)
    {
        $pClass = $this->getProviderClass();

        $pClass->setData(array(
            'AMOUNT' => $donation->amount,
            'ORDER' => str_pad($donation->id, 10, "0", STR_PAD_LEFT),
            'DESC' => substr(iconv("UTF-8", "ASCII//TRANSLIT", $donation->getFullName()), 0, 55),
            'BACKREF' => $donation->getConfirmURL(),
        ));

        $donor = $donation->getOrgDonor();
        $pClass->setData(array(
//            'FIRST_NAME'	   => $donor->first_name,   // nume
//            'LAST_NAME'	   => $donor->last_name,   // prenume            
            'EMAIL' => $donor->email,   // email
        ));

        $pClass->calculatePSIGN();

        $donation->status_notes = $pClass->getDataField('P_SIGN');
        $donation->update();

        return $pClass->generateForm();
    }

    /**
     * @return bool
     */
    public function detectConfirmResponse()
    {
        return $this->detectRequestFields($_REQUEST, array('RC', 'RRN', 'P_SIGN', 'ORDER'));
    }

    /**
     * @return bool
     */
    public function detectIPNResponse()
    {
        return $this->detectRequestFields($_POST, array('amount', 'TRTYPE'));
    }

    public function parseIPNResponse()
    {
        return $this->parseConfirmResponse();
    }

    /**
     * @return bool|\Donation
     */
    public function parseConfirmResponse()
    {
        $donation = Donations::instance()->findOne($_REQUEST['ORDER']);
        if ($donation) {
            $gateway = $donation->getPayment_Method()->getType()->getGateway();
            $pClass = $gateway->initProcesingClass();

            switch ($_REQUEST['TRTYPE']) {
                case 21 :
                case 24 :
                    $typeResponse = 'responseSale';
                    break;
                case 0 :
                default:
                    $typeResponse = 'response';
                    break;
            }

            $pClass->setData($_REQUEST, $typeResponse);
            $pSign = $pClass->getPSIGN($typeResponse);

            if ($_REQUEST['P_SIGN'] != $pSign) {
                $donation->gateway_error = 'Error authetincating message';
                $donation->status = 'error';
            } elseif ($donation->amount != $_REQUEST['AMOUNT']) {
                $donation->status = 'error';
                $donation->gateway_error = 'Eroare autorizare plata';
            } else {
                if ($_REQUEST['ACTION'] == 0) {
                    if ($typeResponse == 'response') {
                        $donation->options['RRN'] = $_REQUEST['RRN'];
                        $donation->options['INT_REF'] = $_REQUEST['INT_REF'];
                        $donation->save();
                        $pClass->sendSaleMessage($donation);
                        die();
                    } else {
                        if ($_REQUEST['TRTYPE'] == '21') {
                            $donation->received = date(DATE_DB);
                            $donation->setStatus('active');
                        }
                    }
                } else {
                    $donation->setStatus('error');
                    $donation->gateway_error = $_REQUEST['MESSAGE'];
                }
            }

        }

        return $donation;
    }

    public function postDonationStatusUpdate($payment)
    {
        if ($payment->getStatus()->getName() == 'canceled') {
            $pClass = $this->initProviderClass();
            $pClass->sendCanceledMessage($payment);
        }
    }

    public function initProviderClass()
    {
        $class = new Romcard();
        $class->setMerchName($this->options['MERCH_NAME'])
            ->setTerminal($this->options['TERMINAL'])
            ->setKey($this->options['KEY']);
        $class->setSandboxMode($this->options['sandbox'] == 'yes');
        return $class;
    }
}