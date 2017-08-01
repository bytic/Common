<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Traits\HasSmartProperties\RecordsTrait;
use Nip\Records\RecordManager as AbstractRecords;

/**
 * Class Records
 * @package ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties
 */
class Records extends AbstractRecords
{
    use RecordsTrait;

    public function registerSmartProperties()
    {
        $this->registerSmartProperty('status');
        $this->registerSmartProperty('registration_status');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function getModelNamespace()
    {
        return 'ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasSmartProperties\\';
    }
}
