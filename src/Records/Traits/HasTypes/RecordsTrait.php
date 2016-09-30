<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

use ByTIC\Common\Records\Types\Generic as GenericType;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasTypes
 */
trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    /**
     * @var GenericType[]
     */
    protected $types = null;

    /**
     * @param $name
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
     * @return GenericType[]|null
     */
    public function getTypes()
    {
        if ($this->types === null) {
            $this->initTypes();
        }

        return $this->types;
    }

    public function initTypes()
    {
        $files = \Nip_File_System::instance()->scanDirectory($this->getTypesDirectory());
        foreach ($files as $name) {
            $name = str_replace('.php', '', $name);
            if (!in_array($name, ['Abstract', 'AbstractType', 'Generic'])) {
                $object = $this->getType($name);
                $this->types[$object->getName()] = $object;
            }
        }
    }

    /**
     * @param string $type
     * @return GenericType
     */
    public function getType($type = null)
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

        return $this->getTypeNamespace().inflector()->classify($type);
    }

    /**
     * @return string
     */
    public function getDefaultType()
    {
        return 'default';
    }

    /**
     * @return string
     */
    public function getTypeNamespace()
    {
        return $this->getModelNamespace().'Types\\';
    }

    /**
     * @return string
     */
    public function getTypesDirectory()
    {
        $rc = new \ReflectionClass(get_class($this));
        $dir = dirname($rc->getFileName());

        return $dir.DIRECTORY_SEPARATOR.'Types';
    }
}
