<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

use ByTIC\Common\Records\Properties\Statuses\Generic;
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

    protected $statusObject;

    /**
     * @return Generic
     */
    public function getStatus()
    {
        if (!$this->statusObject) {
            $this->statusObject = $this->getNewStatus($this->status);
        }
        return $this->statusObject;
    }

    /**
     * @param $status
     * @return Generic
     */
    public function getNewStatus($status)
    {
        $object = clone $this->getManager()->getStatus($status);
        $object->setItem($this);
        return $object;
    }

    /**
     * @param bool $status
     * @return bool|void
     */
    public function setStatus($status = false)
    {
        if (!empty($status)) {
            $newStatus = $this->getNewStatus($status);
            $return = $newStatus->update();
            return $return;
        }
        return false;
    }
}
