<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

use ByTIC\Common\Records\Traits\HasSerializedOptions\RecordTrait;
use Nip\Records\AbstractModels\Record;

/**
 * Class PurchasableRecord
 */
class BillingRecord extends Record
{
    use RecordTrait;

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
