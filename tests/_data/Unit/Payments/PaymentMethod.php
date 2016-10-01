<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

/**
 * Class PurchasableRecord
 */
class PaymentMethod extends \Nip\Records\AbstractModels\Record
{
    use \ByTIC\Common\Records\Traits\HasSerializedOptions\RecordTrait;
    use \ByTIC\Common\Payments\Methods\Traits\RecordTrait;

    public function getRegistry()
    {
    }
}
