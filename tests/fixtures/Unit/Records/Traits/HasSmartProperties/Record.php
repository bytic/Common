<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Traits\HasSmartProperties\RecordTrait;
use Nip\Records\Record as AbstractRecord;

/**
 * Class Record
 * @package ByTIC\Common\Tests\Fixtures\Unit\Recrods\Traits\HasSmartProperties
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

    public function saveRecord()
    {
    }
}
