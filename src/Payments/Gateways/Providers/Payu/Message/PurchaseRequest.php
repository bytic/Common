<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseRequest as AbstractPurchaseRequest;

/**
 * PayU Purchase Request
 */
class PurchaseRequest extends AbstractPurchaseRequest
{

    protected $liveEndpoint = 'https://secure.payu.ro/order/lu.php';
    protected $testEndpoint = 'https://secure.payu.ro/order/lu.php';

    /**
     * @param $value
     * @return mixed
     */
    public function setMerchant($value)
    {
        return $this->setParameter('merchant', $value);
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
            'notifyUrl', 'returnUrl', 'secretKey', 'merchant',
            'card'
        );

        $data = [];

        $this->populateDataOrder($data);
        $this->populateDataOrderItems($data);
        $this->populateDataCard($data);


        $data["ORDER_HASH"] = $this->generateHmac($this->generateHashString($data));

        return $data;
    }

    /**
     * @param $data
     */
    protected function populateDataOrder(&$data)
    {
        $data['MERCHANT'] = $this->getMerchant();
        $data['BACK_REF'] = $this->getReturnUrl();

        $data['ORDER_REF'] = $this->getOrderId();
        $data['ORDER_DATE'] = $this->getOrderDate();

        $data['ORDER_SHIPPING'] = '0';
        $data['ORDER_HASH'] = '';
        $data['PRICES_CURRENCY'] = $this->getCurrency();

        $data['LANGUAGE'] = 'ro';
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
    protected function populateDataOrderItems(&$data)
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

        $data['ORDER_PNAME'] = $name;
        $data['ORDER_PCODE'] = $code;
        $data['ORDER_PRICE'] = $price;
        $data['ORDER_QTY'] = $quantity;
        $data['ORDER_VAT'] = $vat;
    }

    /**
     * @param $data
     */
    protected function populateDataCard(&$data)
    {
        $card = $this->getCard();

        $data['BILL_FNAME'] = $card->getFirstName();
        $data['BILL_LNAME'] = $card->getLastName();
        $data['BILL_CISERIAL'] = '';
        $data['BILL_CINUMBER'] = '';
        $data['BILL_CIISSUER'] = '';
        $data['BILL_CNP'] = '';
        $data['BILL_COMPANY'] = $card->getBillingCompany();
        $data['BILL_FISCALCODE'] = '';
        $data['BILL_REGNUMBER'] = '';
        $data['BILL_BANK'] = '';
        $data['BILL_BANKACCOUNT'] = '';
        $data['BILL_EMAIL'] = $card->getEmail();
        $data['BILL_PHONE'] = $card->getPhone();
        $data['BILL_FAX'] = $card->getFax();
        $data['BILL_ADDRESS'] = $card->getAddress1();
        $data['BILL_ADDRESS2'] = $card->getAddress2();
        $data['BILL_ZIPCODE'] = $card->getPostcode();
        $data['BILL_CITY'] = $card->getCity();
        $data['BILL_STATE'] = $card->getState();
        $data['BILL_COUNTRYCODE'] = Helper::generateCountryCode($card->getCountry());
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateHmac($data)
    {
        $key = $this->getSecretKey();
        return Helper::generateHmac($data, $key);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateHashString(array $data)
    {
        $return = "";
        $return .= Helper::generateHashFromString($data['MERCHANT']);
        $return .= Helper::generateHashFromString($data['ORDER_REF']);
        $return .= Helper::generateHashFromString($data['ORDER_DATE']);

        $return .= Helper::generateHashFromArray($data['ORDER_PNAME']);
        $return .= Helper::generateHashFromArray($data['ORDER_PCODE']);

//        if (is_array($this->orderPInfo) && !empty($this->orderPInfo)) {
//            $retval .= Helper::generateHashFromArray($this->orderPInfo);
//        }

        $return .= Helper::generateHashFromArray($data['ORDER_PRICE']);
        $return .= Helper::generateHashFromArray($data['ORDER_QTY']);
        $return .= Helper::generateHashFromArray($data['ORDER_VAT']);

//        if (is_array($this->orderVer) && !empty($this->orderVer)) {
//            $retval .= Helper::generateHashFromArray($this->orderVer);
//        }

        //if(!empty($this->orderShipping))
        if (is_numeric($data['ORDER_SHIPPING']) && $data['ORDER_SHIPPING'] >= 0) {
            $return .= Helper::generateHashFromString($data['ORDER_SHIPPING']);
        }

        if (is_string($data['PRICES_CURRENCY']) && !empty($data['PRICES_CURRENCY'])) {
            $return .= Helper::generateHashFromString($data['PRICES_CURRENCY']);
        }
//        if (is_numeric($this->discount) && !empty($this->discount)) {
//            $retval .= Helper::generateHashFromString($this->discount);
//        }
//        if (is_string($this->destinationCity) && !empty($this->destinationCity)) {
//            $retval .= Helper::generateHashFromString($this->destinationCity);
//        }
//        if (is_string($this->destinationState) && !empty($this->destinationState)) {
//            $retval .= Helper::generateHashFromString($this->destinationState);
//        }
//        if (is_string($this->destinationCountry) && !empty($this->destinationCountry)) {
//            $retval .= Helper::generateHashFromString($this->destinationCountry);
//        }
//        if (is_string($this->payMethod) && !empty($this->payMethod)) {
//            $retval .= Helper::generateHashFromString($this->payMethod);
//        }
//        if (is_array($this->orderPGroup) && count($this->orderPGroup)) {
//            $retval .= Helper::generateHashFromArray($this->orderPGroup);
//        }
//        if (is_array($this->orderPType) && count($this->orderPType)) {
//            $retval .= Helper::generateHashFromArray($this->orderPType);
//        }
        return $return;
    }

    /**
     * @return string
     */
    public function getCtrl()
    {
        return $this->generateHmac(Helper::generateHashFromString($this->getReturnUrl()));
    }
}
