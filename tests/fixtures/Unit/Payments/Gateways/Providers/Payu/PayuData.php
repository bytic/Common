<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Payu;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class PayuData
 * @package ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Payu
 */
class PayuData
{
    /**
     * @return string
     */
    public static function getMethodOptions()
    {
        $data = 'a:2:{s:15:"payment_gateway";s:4:"payu";s:4:"payu";' .
            'a:2:{s:8:"merchant";s:8:"' . $_ENV['PAYU_MERCHANT'] . '";' .
            's:9:"secretKey";s:20:"' . $_ENV['PAYU_KEY'] . '";'
            . '}}';
        return $data;
    }

    /**
     * @return HttpRequest
     */
    public static function getConfirmAuthorizedRequest()
    {
        $httpRequest = new HttpRequest();

        $httpRequest->query->set('id', '37250');
        $httpRequest->query->set('ctrl', 'a300b00eb8622c89e3f4d47fe1ca6822');

        return $httpRequest;
    }

    /**
     * @return HttpRequest
     */
    public static function getIpnAuthorizedRequest()
    {
        $httpRequest = new HttpRequest();

        $post = 'a:54:{s:8:"SALEDATE";s:19:"2016-10-03 17:21:19";s:5:"REFNO";s:8:"37354984";s:8:"REFNOEXT";s:5:"37250";s:7:"ORDERNO";s:4:"4479";s:11:"ORDERSTATUS";s:18:"PAYMENT_AUTHORIZED";s:9:"PAYMETHOD";s:24:"Visa/MasterCard/Eurocard";s:9:"FIRSTNAME";s:6:"Andrei";s:8:"LASTNAME";s:5:"Voica";s:11:"IDENTITY_NO";s:1:"-";s:15:"IDENTITY_ISSUER";s:0:"";s:12:"IDENTITY_CNP";s:0:"";s:7:"COMPANY";s:0:"";s:18:"REGISTRATIONNUMBER";s:0:"";s:10:"FISCALCODE";s:0:"";s:9:"CBANKNAME";s:0:"";s:12:"CBANKACCOUNT";s:0:"";s:8:"ADDRESS1";s:0:"";s:8:"ADDRESS2";s:0:"";s:4:"CITY";s:0:"";s:5:"STATE";s:0:"";s:7:"ZIPCODE";s:0:"";s:7:"COUNTRY";s:7:"Romania";s:5:"PHONE";s:1:"-";s:3:"FAX";s:0:"";s:13:"CUSTOMEREMAIL";s:22:"andrei_voica@yahoo.com";s:11:"FIRSTNAME_D";s:6:"Andrei";s:10:"LASTNAME_D";s:5:"Voica";s:9:"COMPANY_D";s:0:"";s:10:"ADDRESS1_D";s:8:"Doinei 4";s:10:"ADDRESS2_D";s:0:"";s:6:"CITY_D";s:10:"Alba Iulia";s:7:"STATE_D";s:4:"Alba";s:9:"ZIPCODE_D";s:6:"510138";s:9:"COUNTRY_D";s:7:"Romania";s:7:"PHONE_D";s:10:"0721271171";s:9:"IPADDRESS";s:13:"89.47.231.114";s:8:"CURRENCY";s:3:"RON";s:7:"IPN_PID";a:1:{i:0;s:8:"30545691";}s:9:"IPN_PNAME";a:1:{i:0;s:97:"Donatie pentru Sustine serviciile de ingrijire furnizate de HOSPICE Casa Sperantei via Dana Oprea";}s:9:"IPN_PCODE";a:1:{i:0;s:5:"37250";}s:8:"IPN_INFO";a:1:{i:0;s:0:"";}s:7:"IPN_QTY";a:1:{i:0;s:1:"1";}s:9:"IPN_PRICE";a:1:{i:0;s:5:"30.00";}s:7:"IPN_VAT";a:1:{i:0;s:4:"0.00";}s:7:"IPN_VER";a:1:{i:0;s:0:"";}s:12:"IPN_DISCOUNT";a:1:{i:0;s:4:"0.00";}s:13:"IPN_PROMONAME";a:1:{i:0;s:0:"";}s:18:"IPN_DELIVEREDCODES";a:1:{i:0;s:0:"";}s:9:"IPN_TOTAL";a:1:{i:0;s:5:"30.00";}s:16:"IPN_TOTALGENERAL";s:5:"30.00";s:12:"IPN_SHIPPING";s:4:"0.00";s:14:"IPN_COMMISSION";s:4:"0.60";s:8:"IPN_DATE";s:14:"20161003172145";s:4:"HASH";s:32:"1fa49a9e64b18c4c5e04d281dc6a85cb";}';
        $httpRequest->request->add(unserialize($post));

        return $httpRequest;
    }
}
