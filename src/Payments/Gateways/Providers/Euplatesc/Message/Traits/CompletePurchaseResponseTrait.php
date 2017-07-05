<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits;

use DateTime;

/**
 * Class CompletePurchaseResponseTrait
 * @package ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\Traits
 */
trait CompletePurchaseResponseTrait
{

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->getCode() == 0;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Status code (string)
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getIpnDataItem('action');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->getIpnDataItem('ep_id');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getIpnDataItem('invoice_id');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return false|string
     */
    public function getTransactionDate()
    {
        $timestamp = $this->getIpnDataItem('timestamp');
        $dateTime = DateTime::createFromFormat('YmdHis', $timestamp);

        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Response Message
     *
     * @return null|string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->getDataProperty('message');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return bool
     */
    protected function canProcessModel()
    {
        return true;
    }
}
