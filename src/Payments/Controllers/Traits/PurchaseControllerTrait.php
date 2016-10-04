<?php

namespace ByTIC\Common\Payments\Controllers\Traits;

use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use Nip\Records\RecordManager;

/**
 * Class PurchaseControllerTrait
 * @package ByTIC\Common\Payments\Controllers\Traits
 *
 * @method IsPurchasableModelTrait checkItem
 */
trait PurchaseControllerTrait
{

    public function redirectToPayment()
    {
        $model = $this->checkItem();
        $request = $model->getPurchaseRequest();
        $response = $request->send();
        $response->getView()->set('subtitle', $model->getPurchaseName());
        echo $response->getRedirectResponse()->getContent();
        die();
    }

    public function confirm()
    {
        /** @var ServerCompletePurchaseResponse $response */
        $response = $this->getGatewaysManager()->detectItemFromHttpRequest(
            $this->getModelManager(),
            'serverCompletePurchase'
        );
    }

    /**
     * @return GatewaysManager
     */
    protected function getGatewaysManager()
    {
        return GatewaysManager::instance();
    }

    /**
     * @return RecordManager
     */
    protected abstract function getModelManager();

    public function ipn()
    {
        /** @var ServerCompletePurchaseResponse $response */
        $response = $this->getGatewaysManager()->detectItemFromHttpRequest(
            $this->getModelManager(),
            'serverCompletePurchase'
        );

        $response->send();
    }
}
