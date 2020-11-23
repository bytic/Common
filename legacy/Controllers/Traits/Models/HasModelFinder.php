<?php

namespace ByTIC\Common\Controllers\Traits\Models;

use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;
use Nip\Controllers\Controller;
use Nip\Http\Request;

/**
 * Class HasModelFinder
 * @package ByTIC\Common\Controllers\Traits\Models
 *
 * @deprecated use \ByTIC\Controllers\Behaviors\Models\HasModelFinder;
 */
trait HasModelFinder
{
    use \ByTIC\Controllers\Behaviors\Models\HasModelFinder;
}
