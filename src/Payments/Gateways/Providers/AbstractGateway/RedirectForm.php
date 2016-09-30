<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Traits\PaymentTrait;

/**
 * Class RedirectForm
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway
 */
abstract class RedirectForm
{

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var PaymentTrait
     */
    protected $payment;

    /**
     * @var string|null
     */
    protected $formAction = null;

    /**
     * @return PaymentTrait
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param PaymentTrait $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $this->prepare();
        return $this->generateHTML();
    }

    public function prepare()
    {
    }

    /**
     * @return string
     */
    public function generateHTML()
    {
        $return = $this->renderFormOpenTag();
        $return .= $this->renderFormHidden();
        $return .= $this->renderFormMessages();
        $return .= $this->renderFormImages();
        $return .= $this->renderFormButtons();
        $return .= $this->renderFormCloseTag();

        return $return;
    }

    /**
     * @return string
     */
    protected function renderFormOpenTag()
    {
        return '<form name="form-gateway" 
        method="post" action="' . $this->getFormAction() . '" id="form-gateway" target="_self">';
    }

    /**
     * @return null|string
     */
    public function getFormAction()
    {
        if ($this->formAction === null) {
            $this->initFormAction();
        }
        return $this->formAction;
    }

    /**
     * @param null|string $formAction
     */
    public function setFormAction($formAction)
    {
        $this->formAction = $formAction;
    }

    /**
     * @return void
     */
    abstract protected function initFormAction();

    /**
     * @return string
     */
    protected function renderFormHidden()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function renderFormMessages()
    {
        return '<p class="tx_red_mic">Transferring to ' . $this->getGateway()->getLabel() . ' gateway</p>';
    }

    /**
     * @return Gateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param Gateway $gateway
     * @return $this
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * @return string
     */
    protected function renderFormImages()
    {
        $return = '<p>';
        $return .= '<img src="' . $this->getImageURI() . '" ';
        $return .= ' onload="document.getElementById(\'form-gateway\').submit()"></p>';
        return $return;
    }

    /**
     * @return string
     */
    abstract protected function getImageURI();

    /**
     * @return string
     */
    protected function renderFormButtons()
    {
        return '<p><button id="form-gateway-btn" type="submit" class="btn btn-success btn-large">Go Now!</button></p>';
    }

    /**
     * @return string
     */
    protected function renderFormCloseTag()
    {
        return '</form>';
    }
}
