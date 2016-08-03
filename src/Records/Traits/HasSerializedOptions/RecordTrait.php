<?php

namespace ByTIC\Common\Records\Traits\HasSerializedOptions;

use Nip\Records\Record;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasSerializedOptions
 *
 * @property string $options
 *
 */
trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_options = null;

    public function checkOptions()
    {
        if ($this->_options == null) {
            $this->initOptions();
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $this->checkOptions();
        return $this->_options;
    }

    public function getOption($name)
    {
        $this->checkOptions();
        return $this->_options[$name];
    }

    public function setOption($name, $value)
    {
        $this->checkOptions();
        return $this->_options[$name] = $value;
    }

    public function initOptions()
    {
        $options = unserialize($this->options);
        $options = (is_array($options)) ? $options : array();
        $this->_options = $options;
    }

    public function serializeOptions()
    {
        $this->_options = serialize($this->_options);
    }

}