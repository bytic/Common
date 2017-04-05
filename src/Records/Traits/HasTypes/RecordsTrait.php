<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

use ByTIC\Common\Records\Properties\Types\Generic as GenericType;

/**
 * Class RecordsTrait
 *
 * @package ByTIC\Common\Records\Traits\HasTypes
 */
trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    /**
     * Type array
     *
     * @var GenericType[]
     */
    protected $types = null;

    /**
     * Get property of a type by name
     *
     * @param string $name Type name
     *
     * @return array
     */
    public function getTypeProperty($name)
    {
        $return = [];
        $types = $this->getTypes();

        foreach ($types as $type) {
            $return[] = $type->$name;
        }

        return $return;
    }

    /**
     * Get all the types array
     *
     * @return GenericType[]|null
     */
    public function getTypes()
    {
        $this->checkInitTypes();

        return $this->types;
    }

    /**
     * Check the types have been inited
     *
     * @return void
     */
    public function checkInitTypes()
    {
        if ($this->types === null) {
            $this->initTypes();
        }
    }

    /**
     * Init types
     *
     * @return void
     */
    public function initTypes()
    {
        $names = $this->generateTypesNames();
        foreach ($names as $name) {
            $object = $this->getNewType($name);
            $this->addType($object);
        }
    }

    /**
     * Generate array of type names
     *
     * @return array
     */
    protected function generateTypesNames()
    {
        $files = \Nip_File_System::instance()->scanDirectory(
            $this->getTypesDirectory(),
            true,
            true
        );
        $names = [];
        foreach ($files as $name) {
            $name = $this->generateTypeNameFromPath($name);
            if ($this->checkValidTypeName($name)) {
                $names[] = $name;
            }
        }
        return $names;
    }

    /**
     * Generate the type name from file path
     *
     * @param string $path
     *
     * @return mixed
     */
    protected function generateTypeNameFromPath($path)
    {
        $name = str_replace($this->getTypesDirectory(), '', $path);
        $name = str_replace('.php', '', $name);
        $name = trim($name, DIRECTORY_SEPARATOR);
        return str_replace(DIRECTORY_SEPARATOR, '\\', $name);
    }

    /**
     * Check if givven name is valid type name
     *
     * @param string $name
     *
     * @return bool
     */
    protected function checkValidTypeName($name)
    {
        if (in_array($name, ['Abstract', 'AbstractType', 'Generic'])) {
            return false;
        }
        if (strpos($name, '\AbstractType') !== false) {
            return false;
        }
        if (strpos($name, 'Trait') !== false) {
            return false;
        }
        return true;
    }

    /**
     * Get new type object
     *
     * @param string $type
     *
     * @return GenericType
     */
    public function getNewType($type = null)
    {
        $className = $this->getTypeClass($type);
        /** @var GenericType $object */
        $object = new $className();
        $object->setManager($this);

        return $object;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getTypeClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultType();

        return $this->getTypeNamespace() . inflector()->classify($type);
    }

    /**
     * @return string
     */
    public function getDefaultType()
    {
        return 'default';
    }

    /**
     * @param GenericType $object
     */
    public function addType($object)
    {
        $this->types[$object->getName()] = $object;
    }

    /**
     * @return string
     */
    public function getTypeNamespace()
    {
        return $this->getModelNamespace() . 'Types\\';
    }

    /**
     * @param string $type
     * @return GenericType
     */
    public function getType($type = null)
    {
        $this->checkInitTypes();

        return $this->types[$type];
    }

    /**
     * @return string
     */
    public function getTypesDirectory()
    {
        $rClass = new \ReflectionClass(get_class($this));
        $dir = dirname($rClass->getFileName());

        return $dir . DIRECTORY_SEPARATOR . 'Types';
    }
}
