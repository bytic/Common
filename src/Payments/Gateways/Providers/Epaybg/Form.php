<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Epaybg;

class Form extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Form
{

    public function initElements()
    {
        $this->addRadioGroup('sandbox', 'sandbox', true);
        $element = $this->getForm()->getElement($this->getGateway()->getName() . '[sandbox]');
        $element->getRenderer()->setSeparator('');
        $element->addOption('yes', 'Yes');
        $element->addOption('no', 'No');

        $this->addSelect('paymentpage', 'Payment Page', true);
        $element = $this->getForm()->getElement($this->getGateway()->getName() . '[paymentpage]');
        $element->addOption('paylogin', 'Pay login');
        $element->addOption('credit_paydirect', 'Pay direct');


        $this->addInput('min', 'MIN');
        $this->addInput('secret', 'Secret Key');
    }

}