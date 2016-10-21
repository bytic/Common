<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasFormRecordTrait;
use Nip\Records\Record as NipRecord;
use Nip_Registry;

/**
 * Class Record
 *
 * @property int $id
 * @property string $modified
 * @property string $created
 *
 * @method string getURL($params = [], $module = null)
 * @method Records getManager()
 */
class Record extends NipRecord
{

    use HasFormRecordTrait;

    /**
     * @var Nip_Registry
     */
    protected $registry;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Nip_Registry
     */
    public function getRegistry()
    {
        if (!$this->registry) {
            $this->registry = new Nip_Registry();
        }

        return $this->registry;
    }
}
