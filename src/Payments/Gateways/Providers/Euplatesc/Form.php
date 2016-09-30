<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Form as AbstractForm;

class Form extends AbstractForm
{

    public function initElements()
    {
        $this->addInput('mid', 'MID');
        $this->addInput('key', 'Key');
    }

}