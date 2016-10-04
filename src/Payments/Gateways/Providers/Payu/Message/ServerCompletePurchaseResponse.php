<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse as AbstractResponse;
use Nip\Records\Record;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class ServerCompletePurchaseResponse extends AbstractResponse
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->data['valid'] === true;
    }

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
    public function getContent()
    {
        return '<EPAYMENT>' . $this->data['dateReturn'] . '|' . $this->data['hashReturn'] . '</EPAYMENT>';
    }
}
