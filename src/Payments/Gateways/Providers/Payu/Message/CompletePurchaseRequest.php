<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseRequest as AbstractRequest;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Gateway;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
class CompletePurchaseRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('modelManager');

        if ($this->hasGet('id', 'ctrl')) {
            $data = [];
            $data['valid'] = false;
            if ($this->validateModel() && $this->validateCtrl()) {
                return $data;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validateModel()
    {
        $data['id'] = $this->httpRequest->query->get('id');
        $model = $this->findModel($data['id']);
        if ($model) {
            $data['model'] = $model;

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validateCtrl()
    {
        $data['ctrl'] = $this->httpRequest->query->get('ctrl');

        /** @var IsPurchasableModelTrait $model */
        $model = $data['model'];
        /** @var Gateway $gateway */
        $gateway = $model->getPaymentMethod()->getType()->getGateway();
        $purchaseRequest = $gateway->purchaseFromModel($model);
        $ctrl = $purchaseRequest->getCtrl();
        if ($ctrl == $data['ctrl']) {
            $data['valid'] = true;

            return true;
        }

        return false;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return bool|CompletePurchaseResponse
     */
    public function sendData($data)
    {
        if (is_array($data)) {
            return $this->response = new CompletePurchaseResponse($this, $data);
        }

        return parent::sendData($data);
    }
}
