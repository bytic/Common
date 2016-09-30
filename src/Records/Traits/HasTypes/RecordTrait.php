<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

use ByTIC\Common\Records\Properties\Types\Generic as GenericType;

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
    protected $typeObject = null;

    /**
     * @return GenericType
     */
    public function getTypeObject()
    {
        if ($this->typeObject === null) {
            $this->initTypeObject();
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

    public function initTypeObject()
    {
        $this->typeObject = $this->getNewType($this->getTypeValue());
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getNewType($type)
    {
        $object = clone $this->getManager()->getType($type);
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
