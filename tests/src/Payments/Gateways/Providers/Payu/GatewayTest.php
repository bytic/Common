<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\Payu;

use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Payu\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Payu\PayuData;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway\GatewayTest as AbstractGatewayTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\Payu
 */
class GatewayTest extends AbstractGatewayTest
{

    public function testPurchaseResponse()
    {
//        Debug::debug($this->gateway->getParameters());
        $request = $this->gateway->purchaseFromModel($this->purchase);
//        Debug::debug($this->gateway->getParameters());
//        Debug::debug($request->getParameters());

        /** @var PurchaseResponse $response */
        $response = $request->send();
        self::assertInstanceOf(PurchaseResponse::class, $response);

        $data = $response->getRedirectData();
        self::assertSame('GALANTOM', $data['MERCHANT']);

        $payuResponse = $this->client->post($response->getRedirectUrl(), null, $data)->send();
        self::assertSame(200, $payuResponse->getStatusCode());

        $body = $payuResponse->getBody(true);
        self::assertContains('checkout.php', $body);
        self::assertContains('CART_ID=', $body);
    }

    public function testCompletePurchaseResponse()
    {
        $httpRequest = PayuData::getConfirmAuthorizedRequest();
        $response = $this->doCompletePurchaseResponse($httpRequest);
        self::assertEquals(null, $response->getModel()->status);
    }

    /**
     * @param $httpRequest
     * @return CompletePurchaseResponse
     */
    protected function doCompletePurchaseResponse($httpRequest)
    {
        /** @var CompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            'completePurchase',
            $httpRequest
        );

        self::assertInstanceOf(CompletePurchaseResponse::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertEquals($httpRequest->query->get('id'), $response->getModel()->getPrimaryKey());

        return $response;
    }

    public function testCompletePurchaseResponseAfterServerCompletePurchaseAuthorizedResponse()
    {
        $this->purchase->status = 'active';
        $httpRequest = PayuData::getConfirmAuthorizedRequest();
        $response = $this->doCompletePurchaseResponse($httpRequest);
        self::assertEquals('active', $response->getModel()->status);
    }

    public function testServerCompletePurchaseAuthorizedResponse()
    {
        $httpRequest = PayuData::getIpnAuthorizedRequest();

        /** @var ServerCompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            'serverCompletePurchase',
            $httpRequest
        );

        self::assertInstanceOf(ServerCompletePurchaseResponse::class, $response);
        $data = $response->getData();
        self::assertSame($data['hash'], $data['hmac']);
        self::assertTrue($response->isSuccessful());

        $content = $response->getContent();
        self::assertStringStartsWith('<EPAYMENT>', $content);
        self::assertStringEndsWith('</EPAYMENT>', $content);
    }

    protected function setUp()
    {
        parent::setUp();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->purchase->getPaymentMethod();
        $paymentMethod->options = trim(PayuData::getMethodOptions());

        $this->purchase->created = date('Y-m-d H:i:s');

        $this->gateway = $paymentMethod->getType()->getGateway();
    }
}
