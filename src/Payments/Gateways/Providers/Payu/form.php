<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu;

class Form extends \ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\RedirectForm
{

    public function initElements()
    {
        $this->addInput('merchant', 'Merchant');
        $this->addInput('secretKey', 'Secret Key');
    }
}
