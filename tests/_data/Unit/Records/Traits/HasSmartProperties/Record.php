<?php

namespace ByTIC\Common\Tests\Data\Unit\Records\Traits\HasSmartProperties;

use Nip\Records\Record as AbstractRecord;

/**
 * Class Record
 * @package ByTIC\Common\Tests\Data\Unit\Recrods\Traits\HasSmartProperties
 */
class Record extends AbstractRecord
{
    use \ByTIC\Common\Records\Traits\HasSmartProperties\RecordTrait;


    public function getRegistry()
    {
    }
}
