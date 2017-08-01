<?php

namespace ByTIC\Common\Tests\Unit\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Properties\Definitions\Definition;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\Records;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\RegistrationStatuses\FreeConfirmed;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\RegistrationStatuses\Unpaid;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\Statuses\Allocated;
use ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\Statuses\Applicant;
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
        self::assertCount(2, $definitions);
    }

    public function testGetSmartPropertyDefinition()
    {
        /** @var Definition $statusDefinition */
        $statusDefinition = $this->object->getSmartPropertyDefinition('Status');
        self::assertInstanceOf(Definition::class, $statusDefinition);
        self::assertSame('Status', $statusDefinition->getName());
        self::assertSame('status', $statusDefinition->getField());
        self::assertSame('Statuses', $statusDefinition->getLabel());
        self::assertStringEndsWith('Unit\Records\Traits\HasSmartProperties\Statuses',
            $statusDefinition->getItemsDirectory()
        );

        /** @var Definition $statusDefinition */
        $statusDefinition = $this->object->getSmartPropertyDefinition('RegistrationStatus');
        self::assertInstanceOf(Definition::class, $statusDefinition);
        self::assertSame('RegistrationStatus', $statusDefinition->getName());
        self::assertSame('registration_status', $statusDefinition->getField());
        self::assertSame('RegistrationStatuses', $statusDefinition->getLabel());
        self::assertStringEndsWith('Unit\Records\Traits\HasSmartProperties\RegistrationStatuses',
            $statusDefinition->getItemsDirectory()
        );
    }

    public function testGetSmartPropertyItems()
    {
        $statuses = $this->object->getSmartPropertyItems('Status');
        self::assertCount(2, $statuses);
        self::assertInstanceOf(Allocated::class, $statuses['allocated']);
        self::assertInstanceOf(Applicant::class, $statuses['applicant']);

        $statuses = $this->object->getSmartPropertyItems('RegistrationStatus');
        self::assertCount(4, $statuses);
        self::assertInstanceOf(FreeConfirmed::class, $statuses['free_confirmed']);
        self::assertInstanceOf(Unpaid::class, $statuses['unpaid']);
    }

    public function testGetSmartPropertyValues()
    {
        $values = $this->object->getSmartPropertyValues('Status', 'name');
        self::assertSame(['allocated', 'applicant'], $values);

        $values = $this->object->getSmartPropertyValues('RegistrationStatus', 'name');
        self::assertSame(['free_confirmed', 'paid_confirmed', 'unpaid', 'unregistered'], $values);
    }

    public function testGetSmartPropertyItem()
    {
        $status = $this->object->getSmartPropertyItem('Status', 'allocated');
        self::assertInstanceOf(Allocated::class, $status);
    }


    protected function setUp()
    {
        parent::setUp();
        $this->object = new Records();
    }
}
