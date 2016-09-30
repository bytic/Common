<?php

namespace ByTIC\Common\Tests\Unit\Payments\Methods\Traits;

use ByTIC\Common\Payments\Methods\Traits\RecordsTrait;
use ByTIC\Common\Payments\Methods\Types\CreditCards;
use ByTIC\Common\Tests\Unit\AbstractTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Methods
 */
class RecordsTraitTest extends AbstractTest
{
    /**
     * @var RecordsTrait
     */
    private $traitObject;

    public function testGetTypes()
    {
        $types = $this->traitObject->getTypes();
        self::assertSame(4, count($types));
        self::assertSame(['bank-transfer', 'cash', 'credit-cards', 'waiver'], array_keys($types));
    }

    public function testGetType()
    {
        $type = $this->traitObject->getType('credit-cards');
        static::assertInstanceOf(CreditCards::class, $type);
    }

    protected function _before()
    {
        $this->traitObject = $this->getMockForTrait('ByTIC\Common\Payments\Methods\Traits\RecordsTrait');
    }
}
