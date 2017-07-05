<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Api\Request;

use DOMDocument;
use DOMElement;
use Exception;

/**
 * Class Mobilpay_Payment_Request_Sms
 * This class can be used for accessing mobilpay.ro payment interface for your configured online services
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 *
 */
class Sms extends AbstractRequest
{
    const ERROR_LOAD_FROM_XML_SERVICE_ELEM_MISSING = 0x31000001;
    /**
     * mobilePhone    (Optional)        - MSISDN (mobile phone numner) of the customer.
     * If it's supplied it should be in 07XXXXXXXX format.
     * If it's supplied mobilpay.ro will autocomplete mobile phone field on payment interface
     *
     * @var string(10)
     */
    public $msisdn = null;

    /**
     * Sms constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::PAYMENT_TYPE_SMS;
    }

    /**
     * @param DOMElement $elem
     * @return $this
     * @throws Exception
     */
    protected function loadFromXml(DOMElement $elem)
    {
        parent::parseFromXml($elem);

        //SMS request specific data
        $elems = $elem->getElementsByTagName('service');
        if ($elems->length != 1) {
            throw new Exception('Mobilpay_Payment_Request_Sms::loadFromXml failed: service is missing',
                self::ERROR_LOAD_FROM_XML_SERVICE_ELEM_MISSING);
        }
        $xmlElem = $elems->item(0);
        $this->service = $xmlElem->nodeValue;

        $elems = $elem->getElementsByTagName('msisdn');
        if ($elems->length == 1) {
            $this->msisdn = $elems->item(0)->nodeValue;
        }

        $elem = $elem;

        return $this;
    }

    /**
     * @param $queryString
     * @return $this
     * @throws Exception
     */
    protected function loadFromQueryString($queryString)
    {
        $parameters = explode('&', $queryString);
        $reqParams = [];
        foreach ($parameters as $item) {
            list ($key, $value) = explode('=', $item);
            $reqParams[$key] = urldecode($value);
        }

        if (!isset($reqParams['signature'])) {
            throw new Exception(
                'Mobilpay_Payment_Request_Sms::loadFromQueryString failed: signature is missing',
                self::ERROR_LOAD_FROM_XML_SIGNATURE_ELEM_MISSING
            );
        }
        $this->signature = $reqParams['signature'];
        if (!isset($reqParams['service'])) {
            throw new Exception(
                'Mobilpay_Payment_Request_Sms::loadFromQueryString failed: service is missing',
                self::ERROR_LOAD_FROM_XML_SERVICE_ELEM_MISSING
            );
        }
        $this->service = $reqParams['service'];
        if (!isset($reqParams['tran_id'])) {
            throw new Exception(
                'Mobilpay_Payment_Request_Sms::loadFromQueryString failed: empty order id',
                self::ERROR_LOAD_FROM_XML_ORDER_ID_ATTR_MISSING
            );
        }
        $this->orderId = $reqParams['tran_id'];
        if (isset($reqParams['timestamp'])) {
            $this->timestamp = $reqParams['timestamp'];
        }
        if (isset($reqParams['confirm_url'])) {
            $this->confirmUrl = $reqParams['confirm_url'];
        }
        if (isset($reqParams['return_url'])) {
            $this->confirmUrl = $reqParams['return_url'];
        }
        if (isset($reqParams['msisdn'])) {
            $this->msisdn = $reqParams['msisdn'];
        }
        if (isset($reqParams['first_name'])) {
            $this->params['first_name'] = $reqParams['first_name'];
        }
        if (isset($reqParams['last_name'])) {
            $this->params['last_name'] = $reqParams['last_name'];
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function prepare()
    {
        if (is_null($this->signature) || is_null($this->service) || is_null($this->orderId)) {
            throw new Exception(
                'One or more mandatory properties are invalid!',
                self::ERROR_PREPARE_MANDATORY_PROPERTIES_UNSET
            );
        }

        $this->xmlDoc = new DOMDocument('1.0', 'utf-8');
        $rootElem = $this->xmlDoc->createElement('order');

        //set payment type attribute
        $xmlAttr = $this->xmlDoc->createAttribute('type');
        $xmlAttr->nodeValue = $this->type;
        $rootElem->appendChild($xmlAttr);

        //set id attribute
        $xmlAttr = $this->xmlDoc->createAttribute('id');
        $xmlAttr->nodeValue = $this->orderId;
        $rootElem->appendChild($xmlAttr);

        //set timestamp attribute
        $xmlAttr = $this->xmlDoc->createAttribute('timestamp');
        $xmlAttr->nodeValue = date('YmdHis');
        $rootElem->appendChild($xmlAttr);

        $xmlElem = $this->xmlDoc->createElement('signature');
        $xmlElem->nodeValue = $this->signature;
        $rootElem->appendChild($xmlElem);

        $xmlElem = $this->xmlDoc->createElement('service');
        $xmlElem->nodeValue = $this->service;
        $rootElem->appendChild($xmlElem);

        if (!is_null($this->msisdn)) {
            $xmlElem = $this->xmlDoc->createElement('msisdn');
            $xmlElem->nodeValue = $this->msisdn;
            $rootElem->appendChild($xmlElem);
        }

        if (is_array($this->params) && sizeof($this->params) > 0) {
            $xmlParams = $this->xmlDoc->createElement('params');
            foreach ($this->params as $key => $value) {
                $xmlParam = $this->xmlDoc->createElement('param');

                $xmlName = $this->xmlDoc->createElement('name');
                $xmlName->nodeValue = trim($key);
                $xmlParam->appendChild($xmlName);

                $xmlValue = $this->xmlDoc->createElement('value');
                $xmlValue->appendChild($this->xmlDoc->createCDATASection(urlencode($value)));
                $xmlParam->appendChild($xmlValue);

                $xmlParams->appendChild($xmlParam);
            }

            $rootElem->appendChild($xmlParams);
        }

        if (!is_null($this->returnUrl) || !is_null($this->confirmUrl)) {
            $xmlUrl = $this->xmlDoc->createElement('url');
            if (!is_null($this->returnUrl)) {
                $xmlElem = $this->xmlDoc->createElement('return');
                $xmlElem->nodeValue = $this->returnUrl;
                $xmlUrl->appendChild($xmlElem);
            }
            if (!is_null($this->confirmUrl)) {
                $xmlElem = $this->xmlDoc->createElement('confirm');
                $xmlElem->nodeValue = $this->confirmUrl;
                $xmlUrl->appendChild($xmlElem);
            }

            $rootElem->appendChild($xmlUrl);
        }

        $this->xmlDoc->appendChild($rootElem);

        return $this;
    }
}
