<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Mobilpay;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

/**
 * Class MobilpayData
 * @package ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Mobilpay
 */
class MobilpayData
{
    /**
     * @return string
     */
    public static function getMethodOptions()
    {
        $data = 'a:2:{s:15:"payment_gateway";s:8:"mobilpay";s:8:"mobilpay";' .
            'a:4:{' .
            's:7:"sandbox";s:2:"no";' .
            's:9:"signature";s:24:"' . $_ENV['MOBILPAY_SIGNATURE'] . '";' .
            's:4:"file";s:10:"public.cer";' .
            's:11:"private-key";s:11:"private.key";'
            . '}}';
        return $data;
    }

    /**
     * @return string
     */
    public static function buildCertificates()
    {
        $basePath = TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'PaymentGateways' . DIRECTORY_SEPARATOR;

        if (!file_exists($basePath . 'private.key')) {
            file_put_contents($basePath . 'private.key', gzinflate(base64_decode($_ENV['MOBILPAY_PRIVATE_KEY'])));
        }
        if (!file_exists($basePath . 'public.cer')) {
            file_put_contents($basePath . 'public.cer', gzinflate(base64_decode($_ENV['MOBILPAY_PUBLIC_CER'])));
        }
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

    /** @noinspection PhpMethodNamingConventionInspection
     * @return HttpRequest
     */
    public static function getServerCompletePurchaseRequestInsufficientFonds()
    {
        $httpRequest = new HttpRequest();

        $get = 'a:1:{s:2:"id";s:5:"39188";}';
        $httpRequest->query->add(unserialize($get));

        $httpRequest->request->set(
            'env_key',
            'sdxyTQsrSjUvPUemQGAuCKVFVBqV2fz63ltsWeDqgdeDnkcQqRaETqAiLJc/YoduR5UoYEudYS20SseHCzOlkvTRJ/xz1XUEoUrI7HWZys1noFsnLUbLdBGeMWu6RWF32b1DG8ENl2Ou+HIyQKh5sw7t3UT82id8V/ZndNRlWFY='
        );
        $httpRequest->request->set(
            'data',
            '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'
        );

        return $httpRequest;
    }

    /** @noinspection PhpMethodNamingConventionInspection
     * @return HttpRequest
     */
    public static function getServerCompletePurchaseRequestGenericError35()
    {
        $httpRequest = new HttpRequest();

        $get = 'a:1:{s:2:"id";s:5:"39188";}';
        $httpRequest->query->add(unserialize($get));

        $httpRequest->request->set(
            'env_key',
            'mJFpHoqfozTKLWNcu7RPXN5lcdThI7ZV5SBz12Xvjozzt/PM/IPzPSNVCl7BMv5939MAlQOCj/V3QUP4Zhu1BF/rGMG0LluNbuRYhQaJhbUQEBh4V09K58QRBEnJkW1t0SR/gENUEJwcDAOGNXsefYooQIdLiyIXYAgTU7srNFA='
        );
        $httpRequest->request->set(
            'data',
            '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'
        );

        return $httpRequest;
    }

    /** @noinspection PhpMethodNamingConventionInspection
     * @return HttpRequest
     */
    public static function getServerCompletePurchaseRequestDeclined3DSecure()
    {
        $httpRequest = new HttpRequest();

        $get = 'a:1:{s:2:"id";s:5:"39188";}';
        $httpRequest->query->add(unserialize($get));

        $httpRequest->request->set(
            'env_key',
            'hmZjdYqfoqo+GW4reD7a/NBJ/vqwW2yOw4RHqd63fy+VJWJjywrz7s1ntLaGC82VCk4eJTGRmSb8Uqj1Ltwix5zt4Sdiop88hi5V9VXywOiJi8QyzTtcpy/kTi8uhW37UFHvExu5ohD+f1P444qbzyYkh2hetSJsQCSM1EvMkOQ='
        );
        $httpRequest->request->set(
            'data',
            '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'
        );

        return $httpRequest;
    }
}
