<?php

namespace ByTIC\Common\Records\Traits\HasSerializedProperty;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasSerializedProperty
 *
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @var null|array serialized property values
     */
    protected $spValues = null;

    /**
     * @return array
     */
    public function getSpValues()
    {
        $this->checkSerializedProperty();
        return $this->spValues;
    }

    public function checkSerializedProperty()
    {
        if ($this->spValues == null) {
            $this->initSerializedProperty();
        }
    }

    public function initSerializedProperty()
    {
        $name = $this->getSerializedPropertyName();
        $options = unserialize($this->$name);
        $options = (is_array($options)) ? $options : [];
        $this->spValues = $options;
    }

    /**
     * @return string
     */
    public function getSerializedPropertyName()
    {
        return 'options';
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSerializedProperty($name)
    {
        $this->checkSerializedProperty();
        return $this->spValues[$name];
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setSerializedProperty($name, $value)
    {
        $this->checkSerializedProperty();
        return $this->spValues[$name] = $value;
    }

    public function serializeOptions()
    {
        $name = $this->getSerializedPropertyName();
        $this->$name = serialize($this->spValues);
    }
}
