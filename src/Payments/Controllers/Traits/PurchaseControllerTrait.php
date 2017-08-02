<?php

namespace ByTIC\Common\Payments\Controllers\Traits;

use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Record;
use Nip\Records\RecordManager;
use Nip\Request;

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
        $model = $this->getModelFromRequest();
        $request = $model->getPurchaseRequest();
        $response = $request->send();
        $response->getView()->set('subtitle', $model->getPurchaseName());
        $response->getView()->set('item', $model);
        $response->getView()->set('response', $model);
        echo $response->getRedirectResponse()->getContent();
        die();
    }

    public function confirm()
    {
        $response = $this->getConfirmActionResponse();
        $model = $response->getModel();
        if ($model) {
            $response->processModel();
        }
        $this->confirmProcessResponse($response);
        $response->send();
        die();
    }

    public function ipn()
    {
        $response = $this->getIpnActionResponse();
        $model = $response->getModel();
        if ($model) {
            $response->processModel();
        }
        $this->ipnProcessResponse($response);
        $response->send();
        die();
    }

    /**
     * @return CompletePurchaseResponse
     */
    protected function getConfirmActionResponse()
    {
        /** @var CompletePurchaseResponse $response */
        $response = GatewaysManager::instance()->detectItemFromHttpRequest(
            $this->getModelManager(),
            'completePurchase',
            $this->getRequest()
        );

        if (($response instanceof CompletePurchaseResponse) === false) {
            $this->dispatchAccessDeniedResponse();
        }
        return $response;
    }

    /**
     * @param CompletePurchaseResponse $response
     * @return void
     */
    abstract protected function confirmProcessResponse($response);

    /**
     * @return GatewaysManager
     */
    protected function getGatewaysManager()
    {
        return GatewaysManager::instance();
    }

    /**
     * @return ServerCompletePurchaseResponse
     */
    protected function getIpnActionResponse()
    {
        /** @var ServerCompletePurchaseResponse $response */
        $response = GatewaysManager::instance()->detectItemFromHttpRequest(
            $this->getModelManager(),
            'serverCompletePurchase',
            $this->getRequest()
        );

        if (($response instanceof ServerCompletePurchaseResponse) === false) {
            $this->dispatchAccessDeniedResponse();
        }
        return $response;
    }

    /**
     * @param ServerCompletePurchaseResponse $response
     * @return void
     */
    abstract protected function ipnProcessResponse($response);

    /**
     * @return RecordManager
     */
    abstract protected function getModelManager();

    /**
     * @return Request
     */
    abstract protected function getRequest();

    abstract protected function dispatchAccessDeniedResponse();

    /**
     * @param bool|array $key
     * @return Record|IsPurchasableModelTrait
     */
    abstract protected function getModelFromRequest($key = false);
}
