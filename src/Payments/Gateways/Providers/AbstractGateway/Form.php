<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Forms\Traits\PaymentMethodFormTrait;
use Nip\Form\AbstractForm;
use Nip\Form\Traits\MagicMethodElementsFormTrait;
use Nip_Form as NipForm;

/**
 * Class Form
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway
 */
abstract class Form
{
    use MagicMethodElementsFormTrait;

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var NipForm
     */
    protected $form;

    protected $elements;

    public function init()
    {
        $this->initElements();
        if (is_array($this->elements) && count($this->elements) > 0) {
            $this->getForm()->addDisplayGroup($this->elements, $this->getGateway()->getLabel());
            $this->getForm()->getDisplayGroup($this->getGateway()->getLabel())
                ->setAttrib('class', 'payment_gateway_fieldset')
                ->setAttrib('id', 'payment_gateway_'.$this->getGateway()->getName());
        }
    }

    /**
     * @return void
     */
    public function initElements()
    {
    }

    /**
     * @return NipForm|PaymentMethodFormTrait
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
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
     *
     */
    public function getDataFromModel()
    {
        if (is_array($this->elements) && count($this->elements) > 0) {
            $gName = $this->getGateway()->getName();
            $options = $this->getForm()->getModel()->getOption($gName);
            foreach ($this->elements as $name => $inputName) {
                $element = $this->getForm()->{$inputName};
                $element->setValue($options[$name]);
            }
        }
    }

    /**
     * @param $request
     * @return void
     */
    public function getDataFromRequest($request)
    {
    }

    /**
     * @return bool
     */
    public function processValidation()
    {
        return true;
    }

    public function saveToModel()
    {
        if (is_array($this->elements) && count($this->elements) > 0) {
            $gName = $this->getGateway()->getName();
            if ($this->getForm()->getModel()->getOption('payment_gateway') == $gName) {
                $options = [];
                foreach ($this->elements as $name => $inputName) {
                    $element = $this->getForm()->{$inputName};
                    $options[$name] = $element->getValue();
                }

                $this->getForm()->getModel()->setOption($gName, $options);
            }
        }
    }

    /**
     * @return bool
     */
    public function process()
    {
        return true;
    }

    /**
     * @param $name
     * @param $label
     * @param $type
     * @param $isRequired
     * @return AbstractForm
     */
    protected function add($name, $label, $type, $isRequired)
    {
        return $this->getForm()->add($name, $label, $type, $isRequired);
    }

    /**
     * @param $arguments
     * @return mixed
     */
    protected function getElementNameFromMagicMethodArguments($arguments)
    {
        $name = ''.$this->getGateway()->getName().'['.$arguments[0].']';
        $this->elements[$arguments[0]] = $name;

        return $name;
    }
}
