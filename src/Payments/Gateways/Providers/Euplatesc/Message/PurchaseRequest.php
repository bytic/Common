<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest as AbstractPurchaseRequest;

/**
 * PayU Purchase Request
 */
class PurchaseRequest extends AbstractPurchaseRequest
{

    protected $liveEndpoint = 'https://secure.euplatesc.ro/tdsprocess/tranzactd.php';
    protected $testEndpoint = 'https://secure.euplatesc.ro/tdsprocess/tranzactd.php';

    /**
     * @param $value
     * @return mixed
     */
    public function setMid($value)
    {
        return $this->setParameter('mid', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setKey($value)
    {
        return $this->setParameter('key', $value);
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    public function getData()
    {
        $this->validate(
            'amount', 'currency', 'orderId', 'orderName', 'orderDate',
            'notifyUrl', 'returnUrl', 'key', 'mid',
            'card'
        );

        $data = [];

        $this->populateDataOrder($data);
        $this->populateDataCard($data);


        $data["fp_hash"] = $this->generateHmac($this->generateHashString($data['order']));

        return $data;
    }

    /**
     * @param $data
     */
    protected function populateDataOrder(&$data)
    {
        $data['order'] = [
            'amount' => $this->getAmount(),
            'curr' => $this->getCurrency(),
            'invoice_id' => $this->getOrderId(),
            'order_desc' => $this->getOrderName(),
            'merch_id' => $this->getMid(),
            'timestamp' => gmdate("YmdHis"),
            'nonce' => md5(microtime().mt_rand()),
        ];
    }

    /**
     * @return mixed
     */
    public function getMid()
    {
        return $this->getParameter('mid');
    }

    /**
     * @param $data
     */
    protected function populateDataCard(&$data)
    {
        $card = $this->getCard();

        $data['bill'] = [
            'fname' => $card->getFirstName(),
            'lname' => $card->getLastName(),
            'country' => $card->getCountry(),
            'company' => $card->getBillingCompany(),
            'city' => $card->getCity(),
            'add' => $card->getAddress1().' '.$card->getAddress2(),
            'email' => $card->getEmail(),
            'phone' => $card->getPhone(),
            'fax' => $card->getFax(),
        ];
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateHmac($data)
    {
        $key = $this->getKey();

        return Helper::generateHmac($data, $key);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->getParameter('key');
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateHashString(array $data)
    {
        $return = "";
        foreach ($data as $d) {
            $return .= Helper::generateHashFromString($d);
        }

        return $return;
    }
}
