<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest as AbstractPurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Address;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Invoice;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request\Card;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\Traits\ParamSettersRequestTrait;

/**
 * PayU Purchase Request
 */
class PurchaseRequest extends AbstractPurchaseRequest
{
    use ParamSettersRequestTrait;

    /**
     * @var Card
     */
    protected $cardRequest = null;

    protected $liveEndpoint = 'https://secure.mobilpay.ro';
    protected $testEndpoint = 'http://sandboxsecure.mobilpay.ro';


    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function getData()
    {
        $this->validate(
            'amount', 'currency', 'orderId', 'orderName', 'orderDate',
            'notifyUrl', 'returnUrl', 'signature', 'certificate',
            'card'
        );

        $data = [];
        $this->populateData($data);

        return $data;
    }

    /**
     * @param $data
     */
    protected function populateData(&$data)
    {
        $this->populateMobilpayCardRequest();
        $card = $this->getMobilpayCardRequest();
        $data['env_key'] = $card->getEnvKey();
        $data['data'] = $card->getEncData();
    }

    protected function populateMobilpayCardRequest()
    {
        $card = $this->getMobilpayCardRequest();
        $card->orderId = $this->getOrderId();
        $card->returnUrl = $this->getReturnUrl();
        $card->confirmUrl = $this->getNotifyUrl();
        $card->invoice = $this->generateMobilpayPaymentInvoice();

        $card->encrypt($this->getCertificate());
    }

    /**
     * @return Card
     */
    protected function getMobilpayCardRequest()
    {
        if ($this->cardRequest == null) {
            $this->cardRequest = new Card();
            $this->cardRequest->signature = $this->getSignature();
        }

        return $this->cardRequest;
    }

    /**
     * @return Invoice
     */
    protected function generateMobilpayPaymentInvoice()
    {
        $invoice = new Invoice();
        $invoice->currency = $this->getCurrency();
        $invoice->amount = $this->getAmount();
        $invoice->installments = '2,3';
        $invoice->details = $this->getOrderName();
        $invoice->setBillingAddress($this->generateMobilpayPaymentBillingAddress());
        $invoice->setShippingAddress($this->generateMobilpayPaymentShippingAddress());

        return $invoice;
    }

    /**
     * @return Address
     */
    protected function generateMobilpayPaymentBillingAddress()
    {
        $card = $this->getCard();
        $address = new Address();
        $address->type = 'person'; //company
        $address->firstName = $card->getBillingFirstName();
        $address->lastName = $card->getBillingLastName();
        $address->fiscalNumber = '';
        $address->identityNumber = '';
        $address->country = $card->getBillingCountry();
        $address->county = '';
        $address->city = $card->getBillingCity();
        $address->zipCode = '';
        $address->address = $card->getBillingAddress1().' '.$card->getBillingAddress2();
        $address->email = $card->getEmail();
        $address->mobilePhone = $card->getBillingPhone();
        $address->bank = '';
        $address->iban = '';

        return $address;
    }

    /**
     * @return Address
     */
    protected function generateMobilpayPaymentShippingAddress()
    {
        $card = $this->getCard();
        $address = new Address();
        $address->type = 'person'; //company
        $address->firstName = $card->getShippingFirstName();
        $address->lastName = $card->getShippingLastName();
        $address->fiscalNumber = '';
        $address->identityNumber = '';
        $address->country = $card->getShippingCountry();
        $address->county = '';
        $address->city = $card->getShippingCity();
        $address->zipCode = '';
        $address->address = $card->getShippingAddress1().' '.$card->getShippingAddress2();
        $address->email = $card->getEmail();
        $address->mobilePhone = $card->getShippingPhone();
        $address->bank = '';
        $address->iban = '';

        return $address;
    }
}
