<?php

namespace ByTIC\Common\Tests\Unit\Records\Traits\HasStatus;

use ByTIC\Common\Tests\Data\Unit\Records\Traits\HasStatus\Records;
use ByTIC\Common\Tests\Data\Unit\Records\Traits\HasStatus\Statuses\Allocated;
use ByTIC\Common\Tests\Data\Unit\Records\Traits\HasStatus\Statuses\Applicant;
use ByTIC\Common\Tests\Unit\AbstractTest;

/**
 * Class TraitsTest
 * @package ByTIC\Common\Tests\Unit\Payments\Methods
 */
class RecordsTraitTest extends AbstractTest
{
    /**
     * @var Records
     */
    private $object;

    public function testGetSmartPropertiesDefinitions()
    {
        $definitions = $this->object->getSmartPropertiesDefinitions();
        self::assertCount(1, $definitions);
    }

    public function testGetStatusesDirectory()
    {
        $directory = $this->object->getStatusesDirectory();
        self::assertStringEndsWith('Unit\Records\Traits\HasStatus\Statuses', $directory);
    }

    public function testGetStatuses()
    {
        $statuses = $this->object->getStatuses();
        self::assertCount(2, $statuses);
        self::assertInstanceOf(Allocated::class, $statuses['allocated']);
        self::assertInstanceOf(Applicant::class, $statuses['applicant']);
    }

    public function testGetStatusProperty()
    {
        $values = $this->object->getStatusProperty('name');
        self::assertSame(['allocated', 'applicant'], $values);
    }

    public function testGetStatus()
    {
        $status = $this->object->getStatus('allocated');
        self::assertInstanceOf(Allocated::class, $status);
    }


    protected function setUp()
    {
        parent::setUp();
        $this->object = new Records();
    }
}
