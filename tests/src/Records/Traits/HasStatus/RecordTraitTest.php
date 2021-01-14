<?php

namespace ByTIC\Common\Tests\Records\Traits\HasStatus;

use ByTIC\Models\SmartProperties\Properties\Statuses\Generic;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasStatus\Record;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasStatus\Records;
use ByTIC\Common\Tests\AbstractTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Payments\Methods
 */
class RecordTraitTest extends AbstractTest
{
    /**
     * @var Record
     */
    private $object;

    public function testGetStatusWithoutValue()
    {
        $status = $this->object->getStatus();
        self::assertInstanceOf(Generic::class, $status);
        self::assertSame('allocated', $status->getName());
    }

    public function testGetStatusWithValue()
    {
        $this->object->status = 'applicant';

        $status = $this->object->getStatus();
        self::assertInstanceOf(Generic::class, $status);
        self::assertSame('applicant', $status->getName());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Record();

        $manager = new Records();
        $this->object->setManager($manager);
    }
}
