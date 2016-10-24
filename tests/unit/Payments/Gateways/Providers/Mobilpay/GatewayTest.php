<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\Mobilpay;

use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Gateway;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Mobilpay\MobilpayData;
use ByTIC\Common\Tests\Data\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway\GatewayTest as AbstractGatewayTest;
use Codeception\Util\Debug;
use Mockery as m;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\Mobilpay
 */
class GatewayTest extends AbstractGatewayTest
{

    /**
     * @var Gateway
     */
    protected $gateway;

    public function testPurchaseResponse()
    {
//        Debug::debug($this->gateway->getParameters());
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchaseFromModel($this->purchase);
        self::assertSame('no', $request->getSandbox());
        self::assertSame(false, $request->getTestMode());

        /** @var PurchaseResponse $response */
//        Debug::debug($request->getParameters());
        $response = $request->send();
        self::assertInstanceOf(PurchaseResponse::class, $response);

        $data = $response->getRedirectData();
        Debug::debug($data);
        self::assertCount(2, $data);

        $gatewayResponse = $this->client->post($response->getRedirectUrl(), null, $data)->send();
        self::assertSame(200, $gatewayResponse->getStatusCode());
        self::assertSame('https://secure.mobilpay.ro', $gatewayResponse->getEffectiveUrl());

        //Validate first Response
        $body = $gatewayResponse->getBody(true);
        self::assertContains('ID Tranzactie', $body);
        self::assertContains('Descriere plata', $body);
        self::assertContains('Site comerciant', $body);
    }

    public function testPurchaseResponseSandbox()
    {
//        Debug::debug($this->gateway->getParameters());
        $this->gateway->setSandbox('yes');
        $this->gateway->setTestMode(true);
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchaseFromModel($this->purchase);
        self::assertSame('yes', $request->getSandbox());
        self::assertSame(true, $request->getTestMode());

        /** @var PurchaseResponse $response */
//        Debug::debug($request->getParameters());
        $response = $request->send();
        self::assertInstanceOf(PurchaseResponse::class, $response);

        $data = $response->getRedirectData();
        Debug::debug($data);
        self::assertCount(2, $data);

        $gatewayResponse = $this->client->post($response->getRedirectUrl(), null, $data)->send();
        self::assertSame(200, $gatewayResponse->getStatusCode());
        self::assertSame('http://sandboxsecure.mobilpay.ro', $gatewayResponse->getEffectiveUrl());

        //Validate first Response
        $body = $gatewayResponse->getBody(true);
        self::assertContains('ID Tranzactie', $body);
        self::assertContains('Descriere plata', $body);
        self::assertContains('Site comerciant', $body);
    }

    public function testCompletePurchaseResponse()
    {
        $httpRequest = MobilpayData::getCompletePurchaseRequest();

        /** @var CompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            'completePurchase',
            $httpRequest
        );

        self::assertInstanceOf(CompletePurchaseResponse::class, $response);
        self::assertSame($response->isSuccessful(), $response->getModel()->status == 'active');
        self::assertEquals($httpRequest->query->get('id'), $response->getModel()->id);
    }

    public function testServerCompletePurchaseAuthorizedResponse()
    {
        $httpRequest = MobilpayData::getServerCompletePurchaseRequest();

        /** @var ServerCompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            'serverCompletePurchase',
            $httpRequest
        );

        self::assertInstanceOf(ServerCompletePurchaseResponse::class, $response);
        $data = $response->getData();
        self::assertTrue($response->isSuccessful());
        unset($data['model']);
        self::assertCount(2, $data['ipn_data']);
//
        $content = $response->getContent();
        $validContent = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $validContent .= '<crc>1e59360874ae14eb39c7a038b205bf0d</crc>';
        self::assertSame($validContent, $content);
    }

    protected function setUp()
    {
        parent::setUp();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->purchase->getPaymentMethod();
        $paymentMethod->options = trim(MobilpayData::getMethodOptions());

        $this->purchase->created = date('Y-m-d H:i:s');

        $this->gateway = $paymentMethod->getType()->getGateway();
    }

    /**
     * @param $purchase
     * @return m\Mock
     */
    protected function generatePurchaseManagerMock($purchase)
    {
        $manager = parent::generatePurchaseManagerMock($purchase);

        $purchase->id = 39188;
        $manager->shouldReceive('findOne')->withArgs([39188])->andReturn($purchase);

        return $manager;
    }
}
