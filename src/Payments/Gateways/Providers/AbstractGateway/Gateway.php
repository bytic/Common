<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway;

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
     * @var
     */
    protected $_form;
    protected $_pClass = null;
    protected $_options;

    protected $_model;


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
        if (!$this->label) {
            $this->label = $this->getName();
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

    /**
     * @return string
     */
    public function generateLabel()
    {
        return $this->getNamespaceParentFolder();
    }

    public function initLabel()
    {

    }

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