<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments;

/**
 * Class PurchasableRecord
 */
class PurchasableRecord extends \Nip\Records\AbstractModels\Record
{

    protected $id = 19;

    use \ByTIC\Common\Payments\Traits\IsPurchasableRecordTrait;

    /**
     * @return int
     */
    public function getPurchaseAmount()
    {
        return 10;
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
