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

    protected $_urlPK;

    /**
     * @inheritdoc
     */
    public function requestFilters($request = [])
    {
        $filters = parent::requestFilters($request);

        if (!empty($request['name'])) {
            $filters['name'] = clean($request['name']);
        }

        if (!empty($request['title'])) {
            $filters['title'] = clean($request['title']);
        }

        $filters['page'] = $request['page'];

        return $filters;
    }

    /**
     * @inheritdoc
     */
    public function filter($query, $filters = [])
    {
        $query = parent::filter($query);

        $table = $this->getTable();
        if ($filters['name']) {
            $query->where("$table.name LIKE ?", "%{$filters['name']}%");
        }

        return $query;
    }

    /**
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function getExportByQuery($query, $type = 'excel')
    {
        $name = get_class($this).'_Export_'.ucfirst($type);
        $object = new $name();
        $object->setQuery($query);
        $object->setManager($this);

        return $object;
    }
}
