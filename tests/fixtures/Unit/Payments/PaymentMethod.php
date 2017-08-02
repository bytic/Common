<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments;

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
        return TEST_FIXTURE_PATH . DIRECTORY_SEPARATOR . 'PaymentGateways' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return int
     */
    public function getPurchasesCount()
    {
        return 2;
    }
}
