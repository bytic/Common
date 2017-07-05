<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait as PaymentMethodTrait;
use Nip\Records\AbstractModels\Record;

/**
 * Class PurchasableRecord
 */
class PaymentMethod extends Record
{
    use PaymentMethodTrait;

    public function getRegistry()
    {
    }

    /**
     * @return string
     */
    public function getFilesDirectory()
    {
        return codecept_data_dir('PaymentGateways'.DIRECTORY_SEPARATOR);
    }

    /**
     * @return int
     */
    public function getPurchasesCount()
    {
        return 2;
    }
}
