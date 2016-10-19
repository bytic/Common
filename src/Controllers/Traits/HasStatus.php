<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Records\Traits\HasStatus\RecordsTrait;
use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

/**
 * Class HasStatus
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method Record|RecordTrait getModelFromRequest
 * @method RecordManager|RecordsTrait getModelManager
 */
trait HasStatus
{
    use HasSmartProperty;

    public function initViewStatuses()
    {
        $this->initViewProperty('status');
    }

    public function changeStatus()
    {
        $this->doChangeSmartProperty('status');
    }
}
