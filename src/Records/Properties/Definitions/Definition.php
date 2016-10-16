<?php

namespace ByTIC\Common\Records\Properties\Definitions;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as Property;
use ByTIC\Common\Records\Traits\HasSmartProperties\RecordsTrait;
use Exception;
use Nip\Records\RecordManager;
use Nip_File_System as FileSystem;

/**
 * Class Definition
 * @package ByTIC\Common\Records\Properties\Definitions
 */
class Definition
{

    /**
     * @var RecordManager|RecordsTrait
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $label = null;

    /**
     * @var string
     */
    protected $field;

    protected $items = null;

    protected $itemsDirectory = null;

    protected $defaultValue = null;

    /**
     * @param $name
     * @return Property
     * @throws Exception
     */
    public function getItem($name)
    {
        $items = $this->getItems();
        if (!$this->hasItem($name)) {
            throw new Exception(
                'Bad Item ['.$name.'] for smart property 
                ['.$this->getManager()->getController().']['.$this->getName().']');
        }

        return $items[$name];
    }

    /**
     * @return null|Property[]
     */
    public function getItems()
    {
        if ($this->items == null) {
            $this->initItems();
        }

        return $this->items;
    }

    public function initItems()
    {
        $names = $this->getItemsNames();
        $this->items = [];
        foreach ($names as $name) {
            if (!$this->isAbstractItemName($name)) {
                $object = $this->newStatus($name);
                $this->items[$object->getName()] = $object;
            }
        }
    }

    /**
     * @return array
     */
    public function getItemsNames()
    {
        $files = FileSystem::instance()->scanDirectory($this->getItemsDirectory());
        foreach ($files as &$name) {
            $name = str_replace('.php', '', $name);
        }

        return $files;
    }

    /**
     * @return null|string
     */
    public function getItemsDirectory()
    {
        if ($this->itemsDirectory == null) {
            $this->initItemsDirectory();
        }

        return $this->itemsDirectory;
    }

    public function initItemsDirectory()
    {
        $this->itemsDirectory = $this->generateItemsDirectory();
    }

    /**
     * @return string
     */
    public function generateItemsDirectory()
    {
        return $this->generateManagerDirectory().DIRECTORY_SEPARATOR.$this->generatePropertyDirectory();
    }

    /**
     * @return string
     */
    protected function generateManagerDirectory()
    {
        $reflector = new \ReflectionObject($this->getManager());

        return dirname($reflector->getFileName());
    }

    /**
     * @return RecordManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param RecordManager|RecordsTrait $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return string
     */
    protected function generatePropertyDirectory()
    {
        return $this->getLabel();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            $this->initLabel();
        }

        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    protected function initLabel()
    {
        $name = inflector()->pluralize($this->getName());
        $this->setLabel($name);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->name === null) {
            $this->initName();
        }

        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    protected function initName()
    {
        $name = inflector()->classify($this->getField());
        $this->setName($name);
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isAbstractItemName($name)
    {
        if (in_array($name, ['Abstract', 'Generic'])) {
            return true;
        }
        if (strpos($name, 'Abstract') === 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $type
     * @return Property
     */
    public function newStatus($type = null)
    {
        $className = $this->getItemClass($type);
        $object = new $className();
        /** @var Property $object */
        $object->setManager($this->getManager());

        return $object;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getItemClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultValue();

        return $this->getItemsRootNamespace().inflector()->classify($type);
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        if ($this->defaultValue === null) {
            $this->initDefaultValue();
        }

        return $this->defaultValue;
    }

    /**
     * @param null $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    protected function initDefaultValue()
    {
        $managerDefaultValue = $this->getDefaultValueFromManager();
        if ($managerDefaultValue && $this->hasItem($managerDefaultValue)) {
            $defaultValue = $managerDefaultValue;
        } else {
            $items = $this->getItems();
            $defaultValue = reset(array_keys($items));
        }
        $this->setDefaultValue($defaultValue);
    }

    /**
     * @return bool|string
     */
    protected function getDefaultValueFromManager()
    {
        $method = 'getDefault'.$this->getName();
        if (method_exists($this->getManager(), $method)) {
            return $this->getManager()->{$method}();
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasItem($name)
    {
        $items = $this->getItems();

        return isset($items[$name]);
    }

    /**
     * @return string
     */
    public function getItemsRootNamespace()
    {
        return $this->getManager()->getModelNamespace().$this->getLabel().'\\';
    }

    /**
     * @param $name
     * @return array
     */
    public function getValues($name)
    {
        $return = [];
        $items = $this->getItems();

        foreach ($items as $type) {
            $method = 'get'.ucfirst($name);
            if (method_exists($type, $method)) {
                $return[] = $type->$method();
            } else {
                $return[] = $type->{$name};
            }
        }

        return $return;
    }
}
