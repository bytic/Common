<?php

namespace ByTIC\Common\Tests\Unit\Payments\Gateways\Providers\AbstractGateway;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Methods\Types\CreditCards;
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
        $paymentMethod = new PaymentMethod();
        $type = new CreditCards();
        $type->setItem($paymentMethod);
        $paymentMethod->setType($type);

        $this->purchase = m::mock('ByTIC\Common\Tests\Data\Unit\Payments\PurchasableRecord')->makePartial();
        $this->purchase->shouldReceive('getPaymentMethod')->andReturn($paymentMethod);
    }
}
