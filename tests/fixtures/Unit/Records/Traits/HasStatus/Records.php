<?php

namespace ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasStatus;

use ByTIC\Common\Records\Traits\HasStatus\RecordsTrait;
use Nip\Records\RecordManager as AbstractRecords;

/**
 * Class Records
 * @package ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasStatus
 */
class Records extends AbstractRecords
{
    use RecordsTrait;

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return string
     */
    public function getModelNamespace()
    {
        return 'ByTIC\Common\Tests\Fixtures\Unit\Records\Traits\HasStatus\\';
    }
}
