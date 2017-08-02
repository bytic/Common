<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Euplatesc;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class EuplatescData
 * @package ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Euplatesc
 */
class EuplatescData
{
    /**
     * @return string
     */
    public static function getMethodOptions()
    {
        $data = 'a:2:{s:15:"payment_gateway";s:9:"euplatesc";s:9:"euplatesc";' .
            'a:2:{s:3:"mid";s:11:"' . env('EUPLATESC_MID') . '";' .
            's:3:"key";s:40:"' . env('EUPLATESC_KEY') . '";'
            . '}}';
        return $data;
    }

    /**
     * @return HttpRequest
     */
    public static function getCompletePurchaseRequest()
    {
        $httpRequest = new HttpRequest();

        /** @noinspection LongLine */
        $post = 'a:12:{s:6:"amount";s:5:"50.00";s:4:"curr";s:3:"RON";s:10:"invoice_id";s:5:"37250";s:5:"ep_id";s:40:"ACADA61AF7BB5F33A94F81B91E78EF6D9EEE6800";s:8:"merch_id";s:11:"44840981287";s:6:"action";s:1:"0";s:7:"message";s:8:"Approved";s:8:"approval";s:6:"160858";s:9:"timestamp";s:14:"20161023100340";s:5:"nonce";s:32:"a86fef0d181bb93aaad382476589d80e";s:7:"fp_hash";s:32:"B20CF95958C6BF5A337D3AB60D5D9987";s:4:"lang";s:2:"hu";}';
        $httpRequest->request->add(unserialize($post));

        return $httpRequest;
    }

    /**
     * @return HttpRequest
     */
    public static function getServerCompletePurchaseRequest()
    {
        $httpRequest = new HttpRequest();

        /** @noinspection LongLine */
        $post = 'a:12:{s:6:"amount";s:5:"50.00";s:4:"curr";s:3:"RON";s:10:"invoice_id";s:5:"37250";s:5:"ep_id";s:40:"ACADA61AF7BB5F33A94F81B91E78EF6D9EEE6800";s:8:"merch_id";s:11:"44840981287";s:6:"action";s:1:"0";s:7:"message";s:8:"Approved";s:8:"approval";s:6:"160858";s:9:"timestamp";s:14:"20161023100340";s:5:"nonce";s:32:"a5927e73d601d1e3d9cb70c0106227dc";s:7:"fp_hash";s:32:"E47F2704559D6CC63C065971679F4CD9";s:7:"backurl";s:68:"https://secure.euplatesc.ro/tdsprocess/silent/reply_confirmation.php";}';
        $httpRequest->request->add(unserialize($post));

        return $httpRequest;
    }
}
