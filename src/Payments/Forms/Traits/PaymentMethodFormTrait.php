<?php

namespace ByTIC\Common\Payments\Forms\Traits;

use ByTIC\Common\Forms\Traits\AbstractFormTrait;
use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait as PaymentMethod;
use Nip_Form_Element_Abstract as FormElementAbstract;
use Nip_Form_Element_Select as FormSelect;

/**
 * Class PaymentMethodFormTrait
 * @package ByTIC\Common\Payments\Forms\Traits
 */
trait PaymentMethodFormTrait
{
    use AbstractFormTrait;

    /**
     * @var null|GatewaysManager
     */
    protected $paymentGatewaysManager = null;

    /**
     * @var null|Gateway[]
     */
    protected $paymentGatewaysItems = null;

    /**
     * @var null|array
     */
    protected $paymentGatewaysNames = null;

    protected function initPaymentGatewaysOptionsForm()
    {
        $gateways = $this->getPaymentGatewaysItems();

        foreach ($gateways as $name => $gateway) {
            $gateway->getOptionsForm()->setForm($this)->init();
        }
    }

    /**
     * @return Gateway[]
     */
    public function getPaymentGatewaysItems()
    {
        $this->checkPaymentGatewaysValues();

        return $this->paymentGatewaysItems;
    }

    /**
     * @param null $paymentGatewaysItems
     */
    public function setPaymentGatewaysItems($paymentGatewaysItems)
    {
        $this->paymentGatewaysItems = $paymentGatewaysItems;
    }

    protected function checkPaymentGatewaysValues()
    {
        if ($this->paymentGatewaysItems == null) {
            $this->paymentGatewaysItems = $this->getPaymentGatewaysManager()->getItems();
            $this->paymentGatewaysNames = $this->getPaymentGatewaysManager()->getItemsName();
        }
    }

    /**
     * @return GatewaysManager
     */
    public function getPaymentGatewaysManager()
    {
        if ($this->paymentGatewaysManager == null) {
            $this->initPaymentGatewaysManager();
        }

        return $this->paymentGatewaysManager;
    }

    /**
     * @param null $paymentGatewaysManager
     */
    public function setPaymentGatewaysManager($paymentGatewaysManager)
    {
        $this->paymentGatewaysManager = $paymentGatewaysManager;
    }

    protected function initPaymentGatewaysManager()
    {
        $this->setPaymentGatewaysManager($this->newPaymentGatewaysManager());
    }

    /**
     * @return GatewaysManager
     */
    protected function newPaymentGatewaysManager()
    {
        return GatewaysManager::instance();
    }

    protected function appendPaymentGatewaysOptgroupOption()
    {
        $gateways = $this->getPaymentGatewaysItems();
        /** @var FormSelect $typeInput */
        $typeInput = $this->getElement('type');
        foreach ($gateways as $name => $gateway) {
            $typeInput->appendOptgroupOption(
                $this->getPaymentGatewaysManager()->getLabel('title'),
                $gateway->getName(),
                $gateway->getLabel());
        }
    }

    /**
     * @param $name
     * @return FormElementAbstract
     */
    public abstract function getElement($name);

    /**
     * @param $request
     */
    protected function getDataFromRequestPaymentGateways($request)
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->getDataFromRequest($request);
        }
    }

    protected function getDataFromModelPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->getDataFromModel();
        }
    }

    protected function processValidationPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->processValidation();
        }
    }

    protected function processFormPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->process();
        }
    }

    protected function saveToModelPaymentGateways()
    {
        /** @var FormSelect $typeInput */
        $typeInput = $this->getElement('type');
        $type = $typeInput->getValue();

        if (in_array($type, $this->getPaymentGatewaysNames())) {
            $this->getModel()->type = 'credit-cards';
            $this->getModel()->setOption('payment_gateway', $type);
        }

        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->saveToModel();
        }
    }

    /**
     * @return array|null
     */
    public function getPaymentGatewaysNames()
    {
        $this->checkPaymentGatewaysValues();

        return $this->paymentGatewaysNames;
    }

    /**
     * @return PaymentMethod
     */
    public abstract function getModel();
}
