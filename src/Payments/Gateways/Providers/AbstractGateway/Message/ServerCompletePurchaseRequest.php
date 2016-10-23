<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use Nip\Utility\Traits\NameWorksTrait;

/**
 * Class ServerCompletePurchaseRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class ServerCompletePurchaseRequest extends AbstractRequest
{
    use NameWorksTrait;

    /**
     * @param  array $data
     * @return PurchaseResponse|bool
     */
    public function sendData($data)
    {
        if (is_array($data)) {
            $class = $this->getNamespacePath().'\ServerCompletePurchaseResponse';

            return $this->response = new $class($this, $data);
        }

        return parent::sendData($data);
    }
}
