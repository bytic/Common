<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Payments;

use Nip\Records\AbstractModels\RecordManager;

/**
 * Class PurchasableRecord
 */
class PurchasableRecordManager extends RecordManager
{
    protected $primaryKey = 'id';
}
