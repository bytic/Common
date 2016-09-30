<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

use ByTIC\Common\Records\Types\Generic as GenericType;

/**
 * Class RecordTrait
 *
 * @property $type
 *
 * @method RecordsTrait getManager
 *
 * @package ByTIC\Common\Records\Traits\HasTypes
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @var GenericType
     */
    protected $typeObject;

    /**
     * @return GenericType
     */
    public function getTypeObject()
    {
        if (!$this->typeObject) {
            $this->typeObject = $this->getNewType($this->getTypeValue());
        }

        return $this->typeObject;
    }

    /**
     * @param GenericType $typeObject
     * @return bool
     */
    public function setTypeObject($typeObject = null)
    {
        if (!empty($typeObject)) {
            $newType = $this->getNewType($typeObject);
            $return = $newType->update();

            return $return;
        }

        return false;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getNewType($type)
    {
        $object = $this->getManager()->getType($type);
        $object->setItem($this);

        return $object;
    }

    /**
     * @return string
     */
    public function getTypeValue()
    {
        return $this->type;
    }
}
