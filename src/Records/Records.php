<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Export\AbstractExport;
use ByTIC\Common\Records\Filters\FilterManager;
use ByTIC\Common\Records\Traits\HasForms\RecordsTrait as HasFormsRecordsTrait;
use ByTIC\Common\Records\Traits\HasUrlGeneration\HasUrlGenerationRecordsTrait;
use ByTIC\Common\Records\Traits\I18n\RecordsTrait as I18nRecordsTrait;
use Nip\Records\Filters\Records\HasFiltersRecordsTrait;
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
    use HasFiltersRecordsTrait;

    /**
     * @param $query
     * @param string $type
     * @return AbstractExport
     */
    public function getExportByQuery($query, $type = 'excel')
    {
        $name = get_class($this) . '_Export_' . ucfirst($type);
        /** @var AbstractExport $object */
        $object = new $name();
        $object->setQuery($query);
        $object->setManager($this);

        return $object;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function generateFilterManagerDefaultClass()
    {
        return FilterManager::class;
    }
}
