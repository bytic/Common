<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasView;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\Record;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class CompletePurchaseResponse extends AbstractResponse
{
    use HasView;

    /**
     * @return string
     */
    public function getMessageType()
    {
        $type = 'info';
        switch ($this->getModel()->getStatus()) {
            case 'active':
                $type = 'success';
                break;
            case 'canceled':
                $type = 'error';
                break;
            case 'error':
                $type = 'error';
                break;

            case 'default':
            case 'pending':
                break;
        }

        return $type;
    }

    /**
     * @return Record|RecordTrait|IsPurchasableModelTrait
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
