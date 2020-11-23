<?php

namespace ByTIC\Common\Controllers\Traits\Models;

use Nip\Logger\Exception;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Records\Locator\ModelLocator;

/**
 * Class HasManagerTrait
 * @package ByTIC\Common\Controllers\Traits\Models
 *
 * @deprecated use \ByTIC\Controllers\Behaviors\Models\HasModelManagerTrait;
 */
trait HasModelManagerTrait
{
    use \ByTIC\Controllers\Behaviors\Models\HasModelManagerTrait;
}
