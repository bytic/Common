<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest as AbstractPurchaseRequest;

/**
 * PayU Purchase Request
 */
class PurchaseRequest extends AbstractPurchaseRequest
{
    /**
     * @param $value
     * @return mixed
     */
    public function setMerchant($value)
    {
        return $this->setParameter('merchant', $value);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $this->validate(
            'amount', 'currency', 'orderId', 'orderName', 'orderDate',
            'notifyUrl', 'returnUrl', 'secretKey', 'merchant'
        );

        $data = [];

        $this->populateOrderData($data);
        $this->populateOrderItems($data);

        return $data;
    }

    /**
     * @param $data
     */
    protected function populateOrderData(&$data)
    {
        $data['merchant'] = $this->getMerchant();

        $data['order_ref'] = $this->getOrderId();
        $data['order_date'] = $this->getOrderDate();

        $data['BACK_REF'] = $this->getReturnUrl();
    }

    /**
     * @return mixed
     */
    public function getMerchant()
    {
        return $this->getParameter('merchant');
    }

    /**
     * @param $data
     */
    protected function populateOrderItems(&$data)
    {
        $name = [];
        $code = [];
        $price = [];
        $quantity = [];
        $vat = [];

        $items = $this->getItems();
        if (count($items)) {
//            foreach ($items as $item) {
//            }
        } elseif ($this->getAmount() > 0) {
            $name[] = $this->getOrderName();
            $code[] = $this->getOrderId();
            $price[] = $this->getAmount();
            $quantity[] = 1;
            $vat[] = 0;
        }

        $data['order_pname'] = $name;
        $data['order_pcode'] = $code;
        $data['ORDER_PRICE'] = $price;
        $data['order_qty'] = $quantity;
        $data['order_vat'] = $vat;
    }
}
