<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay;

use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Request\Request\Card;

/**
 * Class Mobilpay
 * @package ByTIC\Common\Payments\Gateways\Providers\Mobilpay
 */
class Mobilpay
{

    /**
     * @var string
     */
    protected $paymentUrl = 'https://secure.mobilpay.ro';

    /**
     * @var string
     */
    protected $paymentUrlTest = 'http://sandboxsecure.mobilpay.ro';
    /**
     *
     * Test Mode
     * Set to true or or 1 for testing mode.
     * @var boolean
     */
    protected $sandboxMode = false;

    protected $signature;

    protected $certificate;

    /**
     * @param $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @param $certificate
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @return string
     */
    public function generateForm()
    {
        $paymentUrl = $this->sandboxMode === true ? $this->paymentUrlTest : $this->paymentUrl;
        $paymentUrl = translator()->getLanguage() == 'ro' ? $paymentUrl : $paymentUrl . '/en/';

        $return = '';
        $return .= '<form name="frmPaymentRedirect" method="post" action="' . $paymentUrl . '" id="form-gateway" name="gateway" target="_self">';
        $return .= '<input type="hidden" name="env_key" value="' . $this->getCardRequest()->getEnvKey() . '"/>';
        $return .= '<input type="hidden" name="data" value="' . $this->getCardRequest()->getEncData() . '"/>';

        $return .= '                                                                 
            <p class="tx_red_mic">Transferring to MobilPay.ro gateway</p>
            <p><img src="' . IMAGES_URL . ').submit()"></p>
            <p><button class="btn btn-success btn-large">Go Now!</button></p>
        </form>';

        return $return;
    }

    /**
     * @return Card
     */
    public function getCardRequest()
    {
        if (!$this->cardRequest) {
            $this->cardRequest = new Card();
            $this->cardRequest->signature = $this->signature;
        }
        return $this->cardRequest;
    }

    /**
     * @param bool $sandboxMode
     * @return bool
     */
    public function setSandboxMode($sandboxMode = false)
    {
        switch ($sandboxMode) {
            case true:
            case 1:
                $this->sandboxMode = true;
                break;

            case false:
            case 0:
            default:
                $this->sandboxMode = false;
        }
        return true;
    }
}
