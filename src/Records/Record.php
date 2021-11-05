<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Traits\HasUrlGeneration\HasUrlGenerationRecordTrait;
use Nip\Collections\Registry;
use Nip\Records\Record as NipRecord;

/**
 * Class Record
 *
 * @property int $id
 * @property string $name
 * @property string $modified
 * @property string $created
 *
 * @method string getURL($params = [], $module = null)
 * @method Records getManager()
 */
class Record extends NipRecord
{
    use \ByTIC\Records\Behaviors\HasForms\HasFormsRecordTrait;

    /**
     * @var Registry
     */
    protected $registry = null;

    /**
     * @param $params
     */
    public function filterURLParams($params)
    {
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getAttributeFromArray('name');
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        if ($this->registry === null) {
            $this->registry = new Registry();
        }

        return $this->registry;
    }
}
