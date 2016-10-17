<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

use ByTIC\Common\Records\Properties\Statuses\Generic;
use ByTIC\Common\Records\Traits\HasSmartProperties\RecordTrait as HasSmartPropertiesRecord;
use Nip\Records\RecordManager;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\HasStatus
 *
 * @property string $status
 * @method RecordManager|RecordsTrait getManager()
 *
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;
    use HasSmartPropertiesRecord;

    /**
     * @return Generic
     */
    public function getStatus()
    {
        return $this->getSmartProperty('Status');
    }

    /**
     * @param $status
     * @return Generic
     */
    public function getNewStatus($status)
    {
        return $this->getNewSmartPropertyFromValue('Status', $status);
    }

    /**
     * @param bool $status
     * @return bool|void
     */
    public function updateStatus($status = false)
    {
        return $this->updateSmartProperty('Status', $status);
    }
}
