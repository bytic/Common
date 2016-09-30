<?php

namespace ByTIC\Common\Payments\Gateways\Mobilpay\Request\Request;

use ByTIC\Common\Payments\Gateways\Mobilpay\Request\Invoice;
use DOMDocument;
use DOMElement;
use Exception;

/**
 * Class Mobilpay_Payment_Request_Card
 * This class can be used for accessing mobilpay.ro payment interface for your configured online services
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 *
 */
class Card extends AbstractRequest
{
    const ERROR_LOAD_FROM_XML_ORDER_INVOICE_ELEM_MISSING = 0x30000001;

    /**
     * @var Invoice
     */
    public $invoice = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::PAYMENT_TYPE_CARD;
    }

    /**
     * @param DOMElement $elem
     * @return $this
     * @throws Exception
     */
    protected function loadFromXml(DOMElement $elem)
    {
        parent::parseFromXml($elem);

        //card request specific data
        $elems = $elem->getElementsByTagName('invoice');
        if ($elems->length != 1) {
            throw new Exception(
                'Mobilpay_Payment_Request_Card::loadFromXml failed; invoice element is missing',
                self::ERROR_LOAD_FROM_XML_ORDER_INVOICE_ELEM_MISSING
            );
        }

        $this->invoice = new Invoice($elems->item(0));

        return $this;
    }

    protected function prepare()
    {
        if (is_null($this->signature) || is_null($this->orderId) || !($this->invoice instanceof Invoice)) {
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

        $xmlElem = $this->invoice->createXmlElement($this->xmlDoc);
        $rootElem->appendChild($xmlElem);

        if (is_array($this->params) && sizeof($this->params) > 0) {
            $xmlParams = $this->xmlDoc->createElement('params');
            foreach ($this->params as $key => $value) {
                $xmlParam = $this->xmlDoc->createElement('param');

                $xmlName = $this->xmlDoc->createElement('name');
                $xmlName->nodeValue = trim($key);
                $xmlParam->appendChild($xmlName);

                $xmlValue = $this->xmlDoc->createElement('value');
                $xmlValue->appendChild($this->xmlDoc->createCDATASection($value));
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
