<?php

namespace ByTIC\Common\Tests\Payments\Gateways\Providers\Mobilpay;

use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Gateway;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\PurchaseRequest;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\PurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\Mobilpay\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Mobilpay\MobilpayData;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Payments\Gateways\Providers\AbstractGateway\GatewayTest as AbstractGatewayTest;
use Mockery as m;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Payments\Providers\Mobilpay
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

    public function testServerCompletePurchaseConfirmedResponse()
    {
        $httpRequest = MobilpayData::getServerCompletePurchaseRequest();
        $response = $this->createServerCompletePurchaseResponse($httpRequest);

        self::assertTrue($response->isSuccessful());

        $content = $response->getContent();
        $validContent = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $validContent .= '<crc>1e59360874ae14eb39c7a038b205bf0d</crc>';
        self::assertSame($validContent, $content);
    }

    /**
     * @param $request
     * @return ServerCompletePurchaseResponse
     */
    protected function createServerCompletePurchaseResponse($request)
    {
        /** @var ServerCompletePurchaseResponse $response */
        $response = $this->gatewayManager->detectItemFromHttpRequest(
            $this->purchaseManager,
            'serverCompletePurchase',
            $request
        );

        self::assertInstanceOf(ServerCompletePurchaseResponse::class, $response);

        self::assertTrue($response->isValid());

        $data = $response->getData();
        self::assertCount(2, $data['ipn_data']);

        return $response;
    }

    /** @noinspection PhpMethodNamingConventionInspection */
    public function testServerCompletePurchaseInsufficientFondsResponse()
    {
        $httpRequest = MobilpayData::getServerCompletePurchaseRequestInsufficientFonds();
        $response = $this->createServerCompletePurchaseResponse($httpRequest);

        self::assertSame('20', $response->getCode());
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());
        self::assertSame('error', $response->getModelResponseStatus());
        self::assertSame('Fonduri insuficiente.', $response->getMessage());

        $content = $response->getContent();
        $validContent = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $validContent .= '<crc>ddd90d683660bbb7581bf5cb942a7207</crc>';
        self::assertSame($validContent, $content);
    }

    /** @noinspection PhpMethodNamingConventionInspection */
    public function testServerCompletePurchaseGenericError35Response()
    {
        $httpRequest = MobilpayData::getServerCompletePurchaseRequestGenericError35();
        $response = $this->createServerCompletePurchaseResponse($httpRequest);

        self::assertSame('35', $response->getCode());
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());
        self::assertSame('error', $response->getModelResponseStatus());
        self::assertSame(' ', $response->getMessage());

        $content = $response->getContent();
        $validContent = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $validContent .= '<crc>bf7a163c9d2c9c3c13de601998bb02c6</crc>';
        self::assertSame($validContent, $content);
    }

    /** @noinspection PhpMethodNamingConventionInspection */
    public function testServerCompletePurchaseDeclined3DSecureResponse()
    {
        $httpRequest = MobilpayData::getServerCompletePurchaseRequestDeclined3DSecure();
        $response = $this->createServerCompletePurchaseResponse($httpRequest);

        self::assertSame('39', $response->getCode());
        self::assertFalse($response->isSuccessful());
        self::assertFalse($response->isPending());
        self::assertFalse($response->isCancelled());
        self::assertSame('error', $response->getModelResponseStatus());
        self::assertSame('gwDeclined3DSecure', $response->getMessage());

        $content = $response->getContent();
        $validContent = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $validContent .= '<crc>0582f27ad4d49f7723dd8cfd91901b0e</crc>';
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
        $manager->shouldReceive('findOne')->withAnyArgs()->andReturn($purchase);

        return $manager;
    }
}
