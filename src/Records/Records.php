<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Traits\HasForms\RecordsTrait as HasFormsRecordsTrait;
use ByTIC\Common\Records\Traits\I18n\RecordsTrait as I18nRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class Records
 *
 * @method string getURL
 */
abstract class Records extends RecordManager
{
    use HasFormsRecordsTrait;
    use I18nRecordsTrait;
}
