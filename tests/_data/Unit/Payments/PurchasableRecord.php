<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use Nip\Records\AbstractModels\Record;

/**
 * Class PurchasableRecord
 */
class PurchasableRecord extends Record
{

    protected $id = 19;

    use IsPurchasableModelTrait;

    /**
     * @return int
     */
    public function getPurchaseAmount()
    {
        return 10.00;
    }

    /**
     * @return string
     */
    public function getConfirmURL()
    {
        return 'http://confirm.ro';
    }

    /**
     * @return string
     */
    public function getIpnURL()
    {
        return 'http://ipn.ro';
    }

    public function getPaymentMethod()
    {
    }
}
