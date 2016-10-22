<?php

namespace ByTIC\Common\Payments\Models\Purchase\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse\RedirectTrait;
use ByTIC\Common\Payments\Models\BillingRecord\Traits\RecordTrait as BillingRecord;
use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait;
use ByTIC\Common\Records\Records;

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
 * @method Records getManager
 */
trait IsPurchasableModelTrait
{
    use IsPurchasableTrait {
        getPurchaseParameters as getPurchaseParametersAbstract;
    }

    /**
     * @return PurchaseRequest|RedirectTrait
     */
    public function getPurchaseRequest()
    {
        return $this->getPaymentMethod()->getGateway()->purchaseFromModel($this);
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
     * @return mixed
     */
    public function getConfirmStatusTitle()
    {
        return $this->getManager()->getMessage('confirm.'.$this->getStatus()->getName());
    }

    /**
     * @return RecordTrait
     */
    abstract public function getPaymentMethod();
}
