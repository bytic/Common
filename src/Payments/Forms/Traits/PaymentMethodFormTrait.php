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

    /**
     * @param $request
     */
    public function getDataFromRequest($request)
    {
        parent::getDataFromRequest($request);

        $this->getDataFromRequestPaymentGateways($request);
    }

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

    public function processValidation()
    {
        parent::processValidation();

        $this->processValidationPaymentGateways();
    }

    protected function processValidationPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->processValidation();
        }
    }

    public function process()
    {
        $this->saveToModel();
        $this->getModel()->save();

        $this->processFormPaymentGateways();
    }

    public function saveToModel()
    {
        parent::saveToModel();

        $this->saveToModelTypeMethod();
        $this->saveToModelPaymentGateways();
    }

    protected function saveToModelTypeMethod()
    {
        /** @var FormSelect $typeInput */
        $typeInput = $this->getElement('type');
        $type = $typeInput->getValue();

        if (in_array($type, $this->getPaymentGatewaysNames())) {
            $this->getModel()->type = 'credit-cards';
            $this->getModel()->setOption('payment_gateway', $type);
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

    protected function saveToModelPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->saveToModel();
        }
    }

    protected function processFormPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->process();
        }
    }

    protected function initNameElements()
    {
        $this->addInput('internal_name', translator()->translate('internal_name'), true);
        $this->addInput('name', translator()->translate('name'), true);

        $this->addDisplayGroup(['internal_name', 'name'], 'Details');
    }

    protected function initTypeElement()
    {
        if ($this->getModel()->getPurchasesCount() > 0) {
            $this->addHidden('type', translator()->translate('type'), true);
            $this->addInput('typeText', translator()->translate('type'), false);
            $this->getElement('typeText')->setAttrib('readonly', 'readonly');
        } else {
            $this->initTypeSelect();
        }

        $this->getElement('type')->setID('payment_type');
    }

    /**
     * @return PaymentMethod
     */
    public abstract function getModel();

    /**
     * @param $name
     * @return FormElementAbstract
     */
    public abstract function getElement($name);

    protected function initTypeSelect()
    {
        $this->addSelect('type', translator()->translate('type'), true);
        $types = $this->getModel()->getManager()->getTypes();
        foreach ($types as $type) {
            $this->getElement('type')->addOption($type->getName(), $type->getLabel());
        }
        $this->appendPaymentGatewaysOptgroupOption();
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

    protected function parseTypeForPaymentGateway()
    {
        if ($this->getModel()->type == 'credit-cards' && $this->getModel()->getOption('payment_gateway')) {
            $this->getElement('type')->setValue($this->getModel()->getOption('payment_gateway'));
        }
    }

    protected function initPaymentGatewaysOptionsForm()
    {
        $gateways = $this->getPaymentGatewaysItems();

        foreach ($gateways as $name => $gateway) {
            $gateway->getOptionsForm()->setForm($this)->init();
        }
    }

    protected function getDataFromModelPaymentGateways()
    {
        $gateways = $this->getPaymentGatewaysItems();
        foreach ($gateways as $gateway) {
            $gateway->getOptionsForm()->getDataFromModel();
        }
    }
}
