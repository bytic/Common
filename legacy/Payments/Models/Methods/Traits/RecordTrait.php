<?php

namespace ByTIC\Common\Payments\Models\Methods\Traits;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Files\MobilpayFile;
use ByTIC\Common\Payments\Models\Methods\Types\AbstractType;
use ByTIC\Common\Payments\Models\Methods\Types\CreditCards;
use ByTIC\Common\Records\Traits\HasTypes\RecordTrait as HasTypesRecordTrait;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Models\Methods\Traits
 * @deprecated use \ByTIC\Payments\Models\Methods\Traits\RecordTrait;
 */
trait RecordTrait
{
    use \ByTIC\Payments\Models\Methods\Traits\RecordTrait;
}
