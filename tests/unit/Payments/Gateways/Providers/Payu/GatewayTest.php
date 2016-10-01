<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;
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
        self::assertSame('PurchaseResponse', $response->getRedirectResponse()->getContent());
    }

    protected function _before()
    {
        parent::_before();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->purchase->getPaymentMethod();
        $paymentMethod->setOption('payment_gateway', 'payu');
        $paymentMethod->setOption('payu', [
            'merchant' => 'GALANTOM',
            'secretKey' => '123',
        ]);

        $this->gateway = $paymentMethod->getType()->getGateway();
    }
}
