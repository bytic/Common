<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;
use ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Payu\PayuData;
use ByTIC\Common\Tests\Data\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway\GatewayTest as AbstractGatewayTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\Payu
 */
class GatewayTest extends AbstractGatewayTest
{

    public function testPurchaseResponse()
    {
        $request = $this->gateway->purchaseFromRecord($this->purchase);
        /** @var PurchaseResponse $response */
        $response = $request->send();
        self::assertInstanceOf('ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse', $response);
        self::assertSame('PurchaseResponse', $response->getRedirectData());
    }

    protected function _before()
    {
        parent::_before();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->purchase->getPaymentMethod();
        $paymentMethod->options = trim(PayuData::getMethodOptions());

        $this->purchase->created = date('Y-m-d H:i:s');

        $this->gateway = $paymentMethod->getType()->getGateway();
    }
}
