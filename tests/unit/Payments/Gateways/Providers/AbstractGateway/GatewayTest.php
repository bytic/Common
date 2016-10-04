<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Tests\Data\Unit\Payments\BillingRecord;
use ByTIC\Common\Tests\Data\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Data\Unit\Payments\PurchasableRecord;
use ByTIC\Common\Tests\Data\Unit\Payments\PurchasableRecordManager;
use ByTIC\Common\Tests\Unit\AbstractTest;
use Mockery as m;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\AbstractGateway
 */
class GatewayTest extends AbstractTest
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


    protected function _before()
    {
        $this->purchase = m::mock(PurchasableRecord::class)->makePartial();

        $paymentMethod = new PaymentMethod();
        $type = new CreditCards();
        $type->setItem($paymentMethod);
        $paymentMethod->setType($type);
        $this->purchase->shouldReceive('getPaymentMethod')->andReturn($paymentMethod);

        $billing = new BillingRecord();
        $this->purchase->shouldReceive('getPurchaseBillingRecord')->andReturn($billing);

        $this->purchaseManager = m::mock(PurchasableRecordManager::class)->makePartial();
        $this->purchaseManager->shouldReceive('getPurchaseBillingRecord')
            ->withArgs([37250])->andReturn($this->purchase);

        $this->client = new \Guzzle\Http\Client();
        $this->gatewayManager = GatewaysManager::instance();
    }
}
