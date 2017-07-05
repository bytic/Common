<?php

namespace ByTIC\Common\Tests\Data\Unit\Records\Traits\HasStatus;

use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\Record as AbstractRecord;

/**
 * Class Record
 * @package ByTIC\Common\Tests\Data\Unit\Recrods\Traits\HasStatus
 *
 * @property string $status
 * @property string $registration_status
 */
class Record extends AbstractRecord
{
    use RecordTrait;

    public function getRegistry()
    {
    }
}
