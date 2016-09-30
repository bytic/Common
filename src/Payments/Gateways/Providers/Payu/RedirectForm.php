<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\RedirectForm as AbstractRedirectForm;

/**
 * Class RedirectForm
 * @package ByTIC\Common\Payments\Gateways\Providers\Payu
 *
 * @method Gateway getGateway
 */
class RedirectForm extends AbstractRedirectForm
{

    public function prepare()
    {
        parent::prepare();

        $payment = $this->getPayment();
        $pClass = $this->getGateway()->getProviderClass();

        $pClass->setOrderRef($payment->id);
        $pClass->setOrderDate($payment->created);

        $PName = [];                                        //products name array
        $PName[] = $payment->getCCName();
        $pClass->setOrderPName($PName);

        $PCode = [];                                        //products code array
        $PCode[] = $payment->id;
        $pClass->setOrderPCode($PCode);

        $PPrice = [];
        $PPrice[] = $payment->getCCAmount();
        $pClass->setOrderPrice($PPrice);

        $PQTY = [];                                        //products qty array
        $PQTY[] = 1;
        $pClass->setOrderQTY($PQTY);

        $PVAT = [];                                        //products vat array
        $PVAT[] = 0;
        $pClass->setOrderVAT($PVAT);

        $payee = $payment->getCCPayee();
        $billing = [
            "billFName" => $payee->first_name,
            "billLName" => $payee->last_name,
            "billCIIssuer" => '',
            "billCNP" => '',
            "billCompany" => '',
            "billFiscalCode" => '',
            "billRegNumber" => '',
            "billBank" => '',
            "billBankAccount" => '',
            "billPhone" => '-',
            "billEmail" => $payee->email,
            "billCountryCode" => 'RO',
        ];

        $pClass->setBilling($billing);

        $returnURL = $payment->getConfirmURL();
        $pClass->setBackRef($payment->getConfirmURL());

        $payment->saveGatewayNote($pClass->hmac(strlen($returnURL).$returnURL));
    }

    /**
     * @return string
     */
    protected function generateFormAction()
    {
        return $this->getGateway()->getProviderClass()->liveUpdateURL;
    }

    /**
     * @return string
     */
    protected function getImageURI()
    {
        $path = dirname(__FILE__).'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'logo.png';

        return $this->generateImageURI($path);
    }
}
