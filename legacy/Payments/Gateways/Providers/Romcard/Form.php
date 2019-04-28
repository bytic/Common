<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Romcard;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Form as AbstractForm;

/**
 * Class Form
 * @deprecated use \ByTIC\Payments
 */
class Form extends AbstractForm
{
    public function initElements()
    {
        $this->addInput('MERCH_NAME', 'MERCH_NAME');
        //$this->addInput('MERCHANT', 'MERCHANT');
        $this->addInput('TERMINAL', 'TERMINAL');
        $this->addInput('KEY', 'KEY');

        $this->addRadioGroup('sandbox', 'sandbox', true);
        $element = $this->getForm()->getElement('romcard[sandbox]');
        $element->getRenderer()->setSeparator('');
        $element->addOption('yes', 'Yes');
        $element->addOption('no', 'No');
    }
}
