<?php

namespace ByTIC\Common\Records\Traits\HasSmartProperties;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as PropertyValue;
use ByTIC\Common\Records\Properties\Definitions\Definition;
use Exception;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasStatus
 */
trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    protected $smartPropertiesDefinitions = null;

    /**
     * @return array
     */
    public function getSmartPropertiesDefinitions()
    {
        $this->checkSmartPropertiesDefinitions();

        return $this->smartPropertiesDefinitions;
    }

    protected function checkSmartPropertiesDefinitions()
    {
        if ($this->smartPropertiesDefinitions === null) {
            $this->initSmartPropertiesDefinitions();
        }
    }

    protected function initSmartPropertiesDefinitions()
    {
        $this->smartPropertiesDefinitions = [];
        $this->registerSmartProperties();
    }

    /**
     * @param $name
     * @return PropertyValue[]|null
     * @throws Exception
     */
    public function getSmartPropertyItems($name)
    {
        $definition = $this->getSmartPropertyDefinition($name);
        if ($definition) {
            return $definition->getItems();
        }
        throw new Exception('invalid smart property ['.$name.']');
    }

    /**
     * @param string $name
     * @return null|Definition
     */
    public function getSmartPropertyDefinition($name)
    {
        if ($this->hasSmartPropertyDefinition($name)) {
            return $this->smartPropertiesDefinitions[$name];
        }

        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasSmartPropertyDefinition($name)
    {
        $this->checkSmartPropertiesDefinitions();

        return isset($this->smartPropertiesDefinitions[$name])
        && $this->smartPropertiesDefinitions[$name] instanceof Definition;
    }

    /**
     * @param $name
     * @param $field
     * @return \ByTIC\Common\Records\Properties\AbstractProperty\Generic[]|null
     * @throws Exception
     */
    public function getSmartPropertyValues($name, $field)
    {
        $definition = $this->getSmartPropertyDefinition($name);
        if ($definition) {
            return $definition->getValues($field);
        }
        throw new Exception('invalid smart property ['.$name.']');
    }

    /**
     * @param string $property
     * @param string $value
     * @return PropertyValue
     * @throws Exception
     */
    public function getSmartPropertyItem($property, $value)
    {
        $definition = $this->getSmartPropertyDefinition($property);
        if ($definition) {
            return $definition->getItem($value);
        }
        throw new Exception('invalid smart property ['.$property.']');
    }

    protected function registerSmartProperties()
    {
    }

    /**
     * @param $field
     */
    protected function registerSmartProperty($field)
    {
        $definition = $this->newSmartPropertyDefinition();
        $definition->setField($field);
        $this->addSmartPropertyDefinition($definition);
    }

    /**
     * @return Definition
     */
    protected function newSmartPropertyDefinition()
    {
        $definition = new Definition();
        $definition->setManager($this);

        return $definition;
    }

    /**
     * @param Definition $definition
     */
    protected function addSmartPropertyDefinition($definition)
    {
        $this->smartPropertiesDefinitions[$definition->getName()] = $definition;
    }
}
