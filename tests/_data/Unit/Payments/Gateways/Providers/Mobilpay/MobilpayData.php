<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Mobilpay;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class MobilpayData
 * @package ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Mobilpay
 */
class MobilpayData
{
    /**
     * @return string
     */
    public static function getMethodOptions()
    {
        return trim(file_get_contents(\Codeception\Configuration::dataDir().'\PaymentGateways\MobilpayOptions.serialized'));
    }

    /**
     * @return HttpRequest
     */
    public static function getCompletePurchaseRequest()
    {
        $httpRequest = new HttpRequest();

        $get = 'a:2:{s:2:"id";s:5:"39188";s:7:"orderId";s:5:"39188";}';
        $httpRequest->query->add(unserialize($get));

        return $httpRequest;
    }

    /**
     * @return HttpRequest
     */
    public static function getServerCompletePurchaseRequest()
    {
        $httpRequest = new HttpRequest();

        $get = 'a:1:{s:2:"id";s:5:"39188";}';
        $httpRequest->query->add(unserialize($get));

        $post = 'a:2:{s:7:"env_key";s:172:"f8bmp/a3cDY/YdD0H8o17FMc9IBK1d50b+D4ImwqwoCdqUKrvZ46yRNVolkivealurS7B+2lXdpJVMet548FfqUGkV951vRf/ZQlI/CQ+OLMwM8pyoF4OFOwZvjija2cg3h6OynLhZnfvomVWnDuw9uUFkQYzr/xr+6s/FoIknA=";s:4:"data";s:2360:"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";}';
        $httpRequest->request->add(unserialize($post));

        return $httpRequest;
    }
}
