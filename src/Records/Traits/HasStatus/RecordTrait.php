<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

use ByTIC\Common\Records\Statuses\Generic;
use Nip\Records\RecordManager;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\HasStatus
 *
 * @property string $status
 *
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_status;

    public function getStatus()
    {
        if (!$this->_status) {
            $this->_status = $this->getNewStatus($this->status);
        }
        return $this->_status;
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