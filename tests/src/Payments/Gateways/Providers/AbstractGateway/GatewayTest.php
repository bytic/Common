<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\BillingRecord;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\PurchasableRecord;
use ByTIC\Common\Tests\Fixtures\Unit\Payments\PurchasableRecordManager;
use ByTIC\Common\Tests\Unit\AbstractTest;
use Mockery as m;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\AbstractGateway
 */
abstract class GatewayTest extends AbstractTest
{
    /**
     * @var GatewaysManager
     */
    protected $gatewayManager;

    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * @var PurchasableRecord
     */
    protected $purchase;

    /**
     * @var PurchasableRecordManager
     */
    protected $purchaseManager;


    protected function setUp()
    {
        parent::setUp();

        $this->purchase = $this->generatePurchaseMock();
        $this->setUpPurchaseManagerMock();

        $paymentMethod = new PaymentMethod();

        $type = new CreditCards();
        $type->setItem($paymentMethod);
        $paymentMethod->setType($type);

        $this->purchase->shouldReceive('getPaymentMethod')->andReturn($paymentMethod);

        $billing = new BillingRecord();
        $this->purchase->shouldReceive('getPurchaseBillingRecord')->andReturn($billing);

        $this->client = new \Guzzle\Http\Client();
        $this->gatewayManager = GatewaysManager::instance();
    }

    /**
     * @return m\Mock
     */
    protected function generatePurchaseMock()
    {
        $purchase = m::mock(PurchasableRecord::class)->makePartial();

        return $purchase;
    }

    protected function setUpPurchaseManagerMock()
    {
        $this->purchaseManager = $this->generatePurchaseManagerMock($this->purchase);
        $this->purchase->setManager($this->purchaseManager);
    }

    /**
     * @param $purchase
     * @return m\Mock
     */
    protected function generatePurchaseManagerMock($purchase)
    {
        $purchaseManager = m::mock(PurchasableRecordManager::class)->makePartial();
        $purchaseManager->shouldReceive('findOne')->withArgs([37250])->andReturn($purchase);

        return $purchaseManager;
    }
}
