<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_type;
    
    public function getType()
    {
        if (!$this->_type) {
            $this->_type = $this->getNewType($this->type);
        }
        return $this->_type;
    }

    public function getNewType($type)
    {
        $object = $this->getManager()->getType($type);
        $object->setItem($this);
        return $object;
    }

    public function setType($type = null)
    {
        if (!empty($type)) {
            $newType = $this->getNewType($type);
            $return = $newType->update();
            return $return;
        }
        return false;
    }
}