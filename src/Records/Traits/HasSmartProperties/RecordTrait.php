<?php

namespace ByTIC\Common\Records\Traits\HasSmartProperties;

use Nip\Records\RecordManager;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\HasStatus
 *
 * @property string $status
 * @method RecordManager|RecordsTrait getManager()
 *
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;
}
