<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

trait RecordsTrait
{
    protected $_statuses = null;
    protected $_statusesPath = null;

    public function getStatusProperty($name)
    {
        $return = array();
        $types = $this->getStatuses();

        foreach ($types as $type) {
            $return[] = $type->$name;
        }

        return $return;
    }

    public function getStatuses()
    {
        if ($this->_statuses == null) {
            $this->initStatuses();
        }
        return $this->_statuses;
    }

    public function initStatuses()
    {
        $files = \Nip_File_System::instance()->scanDirectory($this->getStatusesDirectory());
        $this->_statuses = array();
        foreach ($files as $name) {
            $name = str_replace('.php', '', $name);
            if (!in_array($name, array('Abstract', 'Generic', 'AbstractStatus'))) {
                $object = $this->getStatus($name);
                $this->_statuses[$object->getName()] = $object;
            }
        }
    }

    public function getStatusesDirectory()
    {
        if ($this->_statusesPath == null) {
            $this->initStatusesDirectory();
        }
        return $this->_statusesPath;
    }

    public function initStatusesDirectory()
    {
        $reflector = new \ReflectionObject($this);
        $this->_statusesPath = dirname($reflector->getFilename()) . '/Statuses';
    }

    /**
     * @param string $type
     * @return \ByTIC\Common\Records\Statuses\Generic
     */
    public function getStatus($type = null)
    {
        $className = $this->getStatusClass($type);
        $object = new $className();
        $object->setManager($this);
        return $object;
    }

    public function getStatusClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultStatus();
        return $this->getStatusRootNamespace() . inflector()->classify($this->getController()) . '\Statuses\\' . inflector()->classify($type);
    }

    public function getStatusRootNamespace()
    {
        return 'KM42\Common\Models\\';
    }

    public function getDefaultStatus()
    {
        return 'in-progress';
    }

}