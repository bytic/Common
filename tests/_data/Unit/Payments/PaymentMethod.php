<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait as PaymentMethodTrait;
use ByTIC\Common\Records\Traits\HasSerializedOptions\RecordTrait;
use Nip\Records\AbstractModels\Record;

/**
 * Class PurchasableRecord
 */
class PaymentMethod extends Record
{
    use RecordTrait;
    use PaymentMethodTrait;

    public function getRegistry()
    {
    }
}
