<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Tests\Data\Unit\Payments\BillingRecord;
use ByTIC\Common\Tests\Data\Unit\Payments\PaymentMethod;
use ByTIC\Common\Tests\Data\Unit\Payments\PurchasableRecord;
use ByTIC\Common\Tests\Unit\AbstractTest;
use Mockery as m;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Providers\AbstractGateway
 */
class GatewayTest extends AbstractTest
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var PurchasableRecord
     */
    protected $purchase;


    protected function _before()
    {
        $this->purchase = m::mock('ByTIC\Common\Tests\Data\Unit\Payments\PurchasableRecord')->makePartial();

        $paymentMethod = new PaymentMethod();
        $type = new CreditCards();
        $type->setItem($paymentMethod);
        $paymentMethod->setType($type);
        $this->purchase->shouldReceive('getPaymentMethod')->andReturn($paymentMethod);

        $billing = new BillingRecord();
        $this->purchase->shouldReceive('getPurchaseBillingRecord')->andReturn($billing);
    }
}
