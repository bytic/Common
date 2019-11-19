<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Form as AbstractForm;

/**
 * Class Form
 * @package ByTIC\Common\Payments\Gateways\Providers\Payu
 */
class Form extends AbstractForm
{

    public function initElements()
    {
        $this->addInput('merchant', 'Merchant');
        $this->addInput('secretKey', 'Secret Key');
    }
}
