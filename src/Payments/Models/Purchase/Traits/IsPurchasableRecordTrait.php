<?php

namespace ByTIC\Common\Payments\Models\Purchase\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest;
use ByTIC\Common\Payments\Models\BillingRecord\Traits\RecordTrait as BillingRecord;
use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait;

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

    /**
     * @param $note
     */
    public function saveGatewayNote($note)
    {
        $this->setGatewayNotes($note);
        $this->update();
    }

    /**
     * @param $note
     */
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
     * @return array
     */
    public function getPurchaseParametersCard()
    {
        $params = null;
        $billing = $this->getPurchaseBillingRecord();
        if ($billing) {
            $params = [];
            $params['firstName'] = $billing->getFirstName();
            $params['lastName'] = $billing->getLastName();
            $params['email'] = $billing->getEmail();
        }

        return $params;
    }

    /**
     * @return BillingRecord|null
     */
    public function getPurchaseBillingRecord()
    {
        return null;
    }

    /**
     * @return RecordTrait
     */
    abstract public function getPaymentMethod();
}
