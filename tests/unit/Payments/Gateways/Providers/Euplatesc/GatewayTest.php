<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\Euplatesc;

use ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message\PurchaseResponse;
use ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Euplatesc\EuplatescData;
use ByTIC\Common\Tests\Data\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway\GatewayTest as AbstractGatewayTest;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\Euplatesc
 */
class GatewayTest extends AbstractGatewayTest
{

    public function testPurchaseResponse()
    {
//        Debug::debug($this->gateway->getParameters());
        $request = $this->gateway->purchaseFromModel($this->purchase);

        /** @var PurchaseResponse $response */
//        Debug::debug($request->getParameters());
        $response = $request->send();
        self::assertInstanceOf(PurchaseResponse::class, $response);

        $data = $response->getRedirectData();
//        Debug::debug($data);
        self::assertCount(17, $data);
        self::assertSame('44840981287', $data['merch_id']);

        $gatewayResponse = $this->client->post($response->getRedirectUrl(), null, $data)->send();
        self::assertSame(200, $gatewayResponse->getStatusCode());

        //Validate first Response
        $body = $gatewayResponse->getBody(true);
        $crawler = new Crawler('<body>'.$body.'</body>', $gatewayResponse->getEffectiveUrl());
        $form = $crawler->filter('form')->form();

        self::assertSame('https://secure2.euplatesc.ro/tdsprocess/tranzactd.php', $form->getUri());
        self::assertCount(12, $form->getValues());

        //Validate first Response
        $gatewaySecondResponse = $this->client->post($form->getUri(), null, $form->getValues())->send();
        $body = $gatewaySecondResponse->getBody(true);
        self::assertContains('checkout_plus.php', $body);
        self::assertContains('cart_id=', $body);
    }

    public function testCompletePurchaseResponse()
    {
        $this->testGenericCompletePurchaseResponse('completePurchase');
    }

    /**
     * @param $type
     */
    protected function testGenericCompletePurchaseResponse($type)
    {
        $method = 'get'.ucfirst($type).'Request';
        $httpRequest = EuplatescData::$method();

        /** @var CompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            $type,
            $httpRequest
        );

//        self::assertInstanceOf(CompletePurchaseResponse::class, $response);
        self::assertEquals(0, $response->getCode());
        self::assertEquals('2016-10-23 10:03:40', $response->getTransactionDate());
        self::assertTrue($response->isSuccessful());
        self::assertEquals('active', $response->getModelResponseStatus());
        self::assertEquals($response->getTransactionId(), $response->getModel()->getPrimaryKey());
    }

    public function testServerCompletePurchaseAuthorizedResponse()
    {
        $this->testGenericCompletePurchaseResponse('serverCompletePurchase');
    }

    protected function setUp()
    {
        parent::setUp();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->purchase->getPaymentMethod();
        $paymentMethod->options = trim(EuplatescData::getMethodOptions());

        $this->purchase->created = date('Y-m-d H:i:s');

        $this->gateway = $paymentMethod->getType()->getGateway();
    }
}
