<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

trait RecordTrait
{
    protected $_status;
    
    public function getStatus()
    {
        if (!$this->_status) {
            $this->_status = $this->getNewStatus($this->status);
        }
        return $this->_status;
    }

    public function getNewStatus($status)
    {
        $object = $this->getManager()->getStatus($status);
        $object->setItem($this);
        return $object;
    }

    public function setStatus($status = null)
    {
        if (!empty($status)) {
            $newStatus = $this->getNewStatus($status);
            $return = $newStatus->update();
            return $return;
        }
        return false;
    }
}