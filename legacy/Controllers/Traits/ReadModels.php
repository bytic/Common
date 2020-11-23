<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Database\Query\Select as SelectQuery;
use Nip\Records\Collections\Collection;
use Nip\Records\Record;
use Nip\Records\RecordManager;
use Nip\Request;
use Nip\View;
use Nip_Form as Form;

/**
 * Class ModelsTrait
 * @package ByTIC\Common\Controllers\Traits
 *
 * @deprecated use \ByTIC\Controllers\Behaviors\ReadModels;
 */
trait ReadModels
{
    use \ByTIC\Controllers\Behaviors\ReadModels;
}
