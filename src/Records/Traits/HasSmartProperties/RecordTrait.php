<?php

namespace ByTIC\Common\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as PropertyValue;
use ByTIC\Common\Records\Properties\Definitions\Definition;
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

    /**
     * @var PropertyValue[]
     */
    protected $smartProperties = [];

    /**
     * @param $name
     * @return PropertyValue
     */
    public function getSmartProperty($name)
    {
        $this->checkSmartProperty($name);

        return $this->smartProperties[$name];
    }

    /**
     * @param string $name
     */
    public function checkSmartProperty($name)
    {
        if (!isset($this->smartProperties[$name])) {
            $this->initSmartProperty($name);
        }
    }

    /**
     * @param string $name
     */
    public function initSmartProperty($name)
    {
        if ($this->getManager()->hasSmartPropertyDefinition($name)) {
            $definition = $this->getManager()->getSmartPropertyDefinition($name);
            $property = $this->getNewSmartPropertyFromDefinition($definition);
            $this->setSmartProperty($name, $property);
        }
    }

    /**
     * @param Definition $definition
     * @return PropertyValue
     */
    public function getNewSmartPropertyFromDefinition($definition)
    {
        $name = $definition->getName();
        $value = $this->getSmartPropertyValueFromDefinition($definition);

        return $this->getNewSmartPropertyFromValue($name, $value);
    }

    /**
     * @param Definition $definition
     * @return mixed
     */
    public function getSmartPropertyValueFromDefinition($definition)
    {
        $field = $definition->getField();
        $value = $this->{$field};
        if ($value === null) {
            $value = $definition->getDefaultValue();
        }

        return $value;
    }

    /**
     * @param $name
     * @param $value
     * @return PropertyValue
     */
    public function getNewSmartPropertyFromValue($name, $value)
    {
        $object = clone $this->getManager()->getSmartPropertyItem($name, $value);
        $object->setItem($this);

        return $object;
    }

    /**
     * @param string $name
     * @param PropertyValue $object
     */
    protected function setSmartProperty($name, $object)
    {
        $this->smartProperties[$name] = $object;
    }

    /**
     * @param $name
     * @param $value
     * @return bool|void
     */
    public function updateSmartProperty($name, $value)
    {
        if (!empty($value)) {
            $newStatus = $this->getNewSmartPropertyFromValue($name, $value);
            $return = $newStatus->update();
            $this->setSmartProperty($name, $newStatus);
            return $return;
        }

        return false;
    }
}
