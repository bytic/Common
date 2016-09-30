<?php

namespace ByTIC\Common\Payments\Gateways\AbstractGateway;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\AbstractGateway
 */
abstract class Gateway
{

    protected $name = null;
    protected $label = null;

    protected $_form;
    protected $_pClass = null;
    protected $_options;

    protected $_model;

    public function getModel()
    {
        return $this->_model;
    }

    public function setModel($model)
    {
        $this->_model = $model;
        $type = $model->getOption('payment_gateway');
        $this->setOptions($model->getOption($type));
        return $this;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
    }

    public function getLabel()
    {
        if (!$this->label) {
            $this->label = $this->getName();
        }

        return $this->label;
    }

    public function getName()
    {
        if (!$this->name) {
            $name = get_class($this);
            $name = str_replace('Payment_Gateway_', '', $name);
            $name = inflector()->unclassify($name);
            $this->name = $name;
        }
        return $this->name;
    }

    /**
     * @return Form
     */
    public function getOptionsForm()
    {
        if (!$this->_form) {
            $this->initOptionsForm();
        }
        return $this->_form;
    }

    public function initOptionsForm()
    {
        $this->_form = $this->newOptionsForm();
    }

    /**
     * @return Form
     */
    public function newOptionsForm()
    {
        $class = get_class($this) . '_Form';
        $form = new $class();
        /** @var Form $form */
        $form->setGateway($this);
        return $form;
    }

    public function isActive()
    {
        return true;
    }

    abstract public function generatePaymentForm($payment);

    public function detectConfirmResponse()
    {
        return false;
    }

    public function detectIPNResponse()
    {
        return false;
    }

    /**
     * @return bool|\Donation
     */
    public function parseConfirmResponse()
    {
        return false;
    }

    public function parseIPNResponse()
    {
        return false;
    }

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
     * @param \Donation $donation
     */
    public function postDonationStatusUpdate($donation)
    {
    }

    public function getProcesingClass()
    {
        if ($this->_pClass === null) {
            $this->_pClass = $this->initProcesingClass();

        }
        return $this->_pClass;
    }

    public function initProcesingClass()
    {
        return false;
    }
}