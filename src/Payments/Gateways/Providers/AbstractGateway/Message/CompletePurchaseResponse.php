<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasView;
use Nip\Records\Record;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class CompletePurchaseResponse extends AbstractResponse
{
    use HasView;


    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->data['model'];
    }

    /**
     * @return string
     */
    protected function getViewFile()
    {
        return '/confirm';
    }
}
