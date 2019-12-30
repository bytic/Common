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
    public function initData()
    {
        parent::initData();

        $this->validate('modelManager');

        $this->pushData('valid', false);
        if ($this->validateModel() && $this->validateCtrl()) {
            $this->pushData('valid', true);
        }
    }

    /**
     * @return bool
     */
    public function validateCtrl()
    {
        $ctrl = $this->httpRequest->query->get('ctrl');
        $this->pushData('ctrl', $ctrl);
        $modelCtrl = $this->getModelCtrl();
        $this->pushData('model_ctrl', $modelCtrl);
        if ($ctrl == $modelCtrl) {
            $this->pushData('valid', true);

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getModelCtrl()
    {
        /** @var IsPurchasableModelTrait $model */
        $model = $this->getDataItem('model');
        /** @var Gateway $gateway */
        $gateway = $model->getPaymentMethod()->getType()->getGateway();
        $purchaseRequest = $gateway->purchaseFromModel($model);

        return $purchaseRequest->getCtrl();
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function isProviderRequest()
    {
        return $this->hasGet('ctrl');
    }
}
