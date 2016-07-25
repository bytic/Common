<?php

namespace ByTIC\Common\Records\Traits\HasTypes;

trait RecordsTrait
{
    protected $_types = array();

    public function getTypeProperty($name)
    {
        $return = array();
        $types = $this->getTypes();

        foreach ($types as $type) {
            $return[] = $type->$name;
        }

        return $return;
    }

    public function getTypes()
    {
        if (!$this->_types) {
            $files = \Nip_File_System::instance()->scanDirectory($this->getTypesDirectory());
            foreach ($files as $name) {
                $name = str_replace('.php', '', $name);
                if (!in_array($name, array('Abstract', 'Generic'))) {
                    $object = $this->getType($name);
                    $this->_types[$object->getName()] = $object;
                }
            }
        }
        return $this->_types;
    }

    public function getTypesDirectory()
    {
        $rc = new \ReflectionClass(get_class($this));
        $dir = dirname($rc->getFileName());
        return $dir . DS . 'Types';
    }

    /**
     * @param string $type
     * @return ByTIC\Common\Records\Types\Generic
     */
    public function getType($type = null)
    {
        $className = $this->getTypeClass($type);
        $object = new $className();
        $object->setManager($this);
        return $object;
    }

    public function getTypeClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultType();
        return  $this->getTypeNamespace() . inflector()->classify($type);
    }

    public function getTypeRootNamespace()
    {
        return 'KM42\Common\Models\\';
    }

    public function getTypeNamespace()
    {
        return $this->getTypeRootNamespace() . inflector()->classify($this->getController()) . '\Types\\';
    }

    public function getDefaultType()
    {
        return 'default';
    }

}