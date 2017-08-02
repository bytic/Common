<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments;

use ByTIC\Common\Payments\Models\BillingRecord\Traits\RecordTrait as BillingRecordTrait;
use ByTIC\Common\Records\Traits\HasSerializedOptions\RecordTrait as HasSerializedOptions;
use Nip\Records\AbstractModels\Record;

/**
 * Class PurchasableRecord
 */
class BillingRecord extends Record
{
    use HasSerializedOptions;
    use BillingRecordTrait;

    public $phone = '99';

    /**
     * @return string
     */
    public function getFirstName()
    {
        return 'John';
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return 'Doe';
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return 'john@doe.com';
    }

    public function getRegistry()
    {
    }
}
