<?php

namespace ByTIC\Common\Payments\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest;
use ByTIC\Common\Payments\Methods\Traits\RecordTrait;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 *
 * @property int $id
 * @property string $status_notes
 * @property string $created
 *
 * @method string getConfirmURL
 * @method string getIpnURL
 */
trait IsPurchasableRecordTrait
{
    use IsPurchasableTrait {
        getPurchaseParameters as getPurchaseParametersAbstract;
    }

    /**
     * @return PurchaseRequest
     */
    public function getPurchaseRequest()
    {
        return $this->getPaymentMethod()->getGateway()->purchaseFromRecord($this);
    }

    public function saveGatewayNote($note)
    {
        $this->setGatewayNotes($note);
        $this->update();
    }

    public function setGatewayNotes($note)
    {
        $this->status_notes = $note;
    }

    /**
     * @return array
     */
    public function getPurchaseParameters()
    {
        $parameters = $this->getPurchaseParametersAbstract();
        $gatewayParams = $this->getPaymentMethod()->getType()->getGatewayOptions();
        $parameters = array_merge($parameters, $gatewayParams);

        return $parameters;
    }

    /**
     * @return RecordTrait
     */
    abstract public function getPaymentMethod();
}
