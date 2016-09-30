<?php

namespace ByTIC\Common\Payments\Gateways\Payu;

class Form extends \ByTIC\Common\Payments\Gateways\AbstractGateway\Form
{

    public function initElements()
    {
        $this->addInput('merchant', 'Merchant');
        $this->addInput('secretKey', 'Secret Key');
    }
}
