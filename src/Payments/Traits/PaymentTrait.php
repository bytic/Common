<?php

namespace ByTIC\Common\Payments\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\RedirectForm;
use ByTIC\Common\Payments\Methods\Traits\RecordTrait as PaymentMethod;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Traits
 *
 * @property int $id
 * @property string $status_notes
 * @property string $created
 *
 * @method string getConfirmURL
 */
trait PaymentTrait
{

    /**
     * @return mixed
     */
    public function generateGatewayRedirectForm()
    {
        return $this->getGatewayRedirectForm()->generate();
    }

    /**
     * @return RedirectForm
     */
    public function getGatewayRedirectForm()
    {
        $gateway = $this->getPaymentMethod()->getGateway();

        return $gateway->getRedirectForm($this);
    }

    /**
     * @return PaymentMethod
     */
    abstract public function getPaymentMethod();

    public function saveGatewayNote($note)
    {
        $this->setGatewayNotes($note);
        $this->update();
    }

    public function setGatewayNotes($note)
    {
        $this->status_notes = $note;
    }

    abstract public function getCCName();

    abstract public function getCCAmount();

    abstract public function getCCPayee();
}
