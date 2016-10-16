<?php

namespace ByTIC\Common\Tests\Unit\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic;
use ByTIC\Common\Tests\Data\Unit\Records\Traits\HasSmartProperties\Record;
use ByTIC\Common\Tests\Data\Unit\Records\Traits\HasSmartProperties\Records;
use ByTIC\Common\Tests\Unit\AbstractTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Methods
 */
class RecordTraitTest extends AbstractTest
{
    /**
     * @var Record
     */
    private $object;

    public function testGetPropertyWithoutValue()
    {
        $status = $this->object->getSmartProperty('Status');
        self::assertInstanceOf(Generic::class, $status);
        self::assertSame('allocated', $status->getName());

        $registrationStatus = $this->object->getSmartProperty('RegistrationStatus');
        self::assertInstanceOf(Generic::class, $registrationStatus);
        self::assertSame('free_confirmed', $registrationStatus->getName());
    }

    public function testGetStatusWithValue()
    {
        $this->object->status = 'applicant';
        $this->object->registration_status = 'unpaid';

        $status = $this->object->getSmartProperty('Status');
        self::assertInstanceOf(Generic::class, $status);
        self::assertSame('applicant', $status->getName());

        $registrationStatus = $this->object->getSmartProperty('RegistrationStatus');
        self::assertInstanceOf(Generic::class, $registrationStatus);
        self::assertSame('unpaid', $registrationStatus->getName());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->object = new Record();

        $manager = new Records();
        $this->object->setManager($manager);
    }
}
