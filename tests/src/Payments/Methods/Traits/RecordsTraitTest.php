<?php

namespace ByTIC\Common\Tests\Payments\Methods\Traits;

use ByTIC\Common\Payments\Models\Methods\Traits\RecordsTrait;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Tests\AbstractTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Payments\Methods
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

    protected function setUp()
    {
        parent::setUp();
        $this->traitObject = $this->getMockForTrait('ByTIC\Common\Payments\Models\Methods\Traits\RecordsTrait');
    }
}
