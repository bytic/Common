<?php

namespace ByTIC\Common\Records\Traits\HasSerializedOptions;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasSerializedOptions
 *
 * @property string $options
 *
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @var null|array
     */
    protected $optionsArray = null;

    /**
     * @return array
     */
    public function getOptions()
    {
        $this->checkOptions();
        return $this->optionsArray;
    }

    public function checkOptions()
    {
        if ($this->optionsArray == null) {
            $this->initOptions();
        }
    }

    public function initOptions()
    {
        $options = unserialize($this->options);
        $options = (is_array($options)) ? $options : [];
        $this->optionsArray = $options;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        $this->checkOptions();
        return isset($this->optionsArray[$name]) ? $this->optionsArray[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setOption($name, $value)
    {
        $this->checkOptions();
        return $this->optionsArray[$name] = $value;
    }

    public function serializeOptions()
    {
        $this->options = serialize($this->getOptions());
    }
}
