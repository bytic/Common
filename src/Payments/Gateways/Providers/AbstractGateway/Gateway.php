<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Traits\PaymentTrait;
use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway
 */
abstract class Gateway
{

    use NameWorksTrait;

    /**
     * @var null|string
     */
    protected $name = null;

    /**
     * @var null|string
     */
    protected $label = null;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var RedirectForm
     */
    protected $redirectForm;

    /**
     * @var null
     */
    protected $providerClass = null;

    protected $options;

    /**
     * @var PaymentTrait
     */
    protected $paymentMethodModel;


    /**
     * @return null|string
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->initName();
        }
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function initName()
    {
        $this->setName($this->generateName());
    }

    /**
     * @return string
     */
    protected function generateName()
    {
        return strtolower($this->getLabel());
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            $this->initLabel();
        }

        return $this->label;
    }

    /**
     * @param null|string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function initLabel()
    {
        $this->setLabel($this->generateLabel());
    }

    /**
     * @return string
     */
    public function generateLabel()
    {
        return $this->getNamespaceParentFolder();
    }

    /**
     * @return PaymentTrait
     */
    public function getPaymentMethodModel()
    {
        return $this->paymentMethodModel;
    }

    /**
     * @param  PaymentTrait $paymentMethodModel
     * @return $this
     */
    public function setPaymentMethodModel($paymentMethodModel)
    {
        $this->paymentMethodModel = $paymentMethodModel;
        $this->setOptions($paymentMethodModel->getPaymentGatewayOptions());
        return $this;
    }

    /**
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return Form
     */
    public function getOptionsForm()
    {
        if (!$this->form) {
            $this->initOptionsForm();
        }
        return $this->form;
    }

    public function initOptionsForm()
    {
        $this->form = $this->newOptionsForm();
    }

    /**
     * @return Form
     */
    public function newOptionsForm()
    {
        $class = $this->getNamespacePath() . '\Form';
        $form = new $class();
        /** @var Form $form */
        $form->setGateway($this);
        return $form;
    }

    /**
     * @param PaymentTrait $payment
     * @return RedirectForm
     */
    public function getRedirectForm($payment)
    {
        $form = $this->newRedirectForm();
        $form->setPayment($payment);
        return $form;
    }

    /**
     * @return RedirectForm
     */
    public function newRedirectForm()
    {
        $class = $this->getNamespacePath() . '\RedirectForm';
        /** @var RedirectForm $form */
        $form = new $class();
        $form->setGateway($this);
        return $form;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function detectConfirmResponse()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function detectIPNResponse()
    {
        return false;
    }

    /**
     * @return bool|PaymentTrait
     */
    public function parseConfirmResponse()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function parseIPNResponse()
    {
        return false;
    }

    /**
     * @param $request
     * @param array $fields
     * @return bool
     */
    public function detectRequestFields($request, $fields = [])
    {
        foreach ($fields as $field) {
            if (!isset($request[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $payment
     */
    public function postDonationStatusUpdate($payment)
    {
    }

    /**
     * @return null
     */
    public function getProviderClass()
    {
        if ($this->providerClass === null) {
            $this->initProviderClass();

        }
        return $this->providerClass;
    }

    /**
     * @param null $providerClass
     */
    public function setProviderClass($providerClass)
    {
        $this->providerClass = $providerClass;
    }

    public function initProviderClass()
    {
        $this->setProviderClass($this->generateProviderClass());
    }

    abstract protected function generateProviderClass();
}
