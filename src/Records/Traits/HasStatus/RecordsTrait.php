<?php

namespace ByTIC\Common\Records\Traits\HasStatus;

use ByTIC\Common\Records\Properties\Statuses\Generic as GenericStatus;
use Exception;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasStatus
 */
trait RecordsTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    protected $statuses = null;

    protected $statusesPath = null;

    /**
     * @param $name
     * @return array
     */
    public function getStatusProperty($name)
    {
        $return = [];
        $types = $this->getStatuses();

        foreach ($types as $type) {
            $return[] = $type->{$name};
        }

        return $return;
    }

    /**
     * @return null|GenericStatus[]
     */
    public function getStatuses()
    {
        if ($this->statuses == null) {
            $this->initStatuses();
        }
        return $this->statuses;
    }

    public function initStatuses()
    {
        $names = $this->getStatusesNames();
        $this->statuses = [];
        foreach ($names as $name) {
            if (!$this->isIgnoredStatusesName($name)) {
                $object = $this->newStatus($name);
                $this->statuses[$object->getName()] = $object;
            }
        }
    }

    /**
     * @return array
     */
    public function getStatusesNames()
    {
        $files = \Nip_File_System::instance()->scanDirectory($this->getStatusesDirectory());
        foreach ($files as &$name) {
            $name = str_replace('.php', '', $name);
        }
        return $files;
    }

    /**
     * @return null|string
     */
    public function getStatusesDirectory()
    {
        if ($this->statusesPath == null) {
            $this->initStatusesDirectory();
        }
        return $this->statusesPath;
    }

    public function initStatusesDirectory()
    {
        $reflector = new \ReflectionObject($this);
        $this->statusesPath = dirname($reflector->getFileName()) . '/Statuses';
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isIgnoredStatusesName($name)
    {
        return in_array($name, $this->getIgnoredStatusesNames());
    }

    /**
     * @return array
     */
    public function getIgnoredStatusesNames()
    {
        return ['Abstract', 'Generic', 'AbstractStatus'];
    }

    /**
     * @param string $type
     * @return GenericStatus
     */
    public function newStatus($type = null)
    {
        $className = $this->getStatusClass($type);
        $object = new $className();
        /** @var GenericStatus $object */
        $object->setManager($this);
        return $object;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getStatusClass($type = null)
    {
        $type = $type ? $type : $this->getDefaultStatus();
        return $this->getStatusRootNamespace() . inflector()->classify($type);
    }

    /**
     * @return string
     */
    public function getDefaultStatus()
    {
        return 'in-progress';
    }

    /**
     * @return string
     */
    public function getStatusRootNamespace()
    {
        return $this->getModelNamespace() . 'Statuses\\';
    }

    /**
     * @param string $name
     * @return GenericStatus
     * @throws Exception
     */
    public function getStatus($name = null)
    {
        $statuses = $this->getStatuses();
        if (!isset($statuses[$name])) {
            throw new Exception('Bad status [' . $name . '] for [' . $this->getController() . ']');
        }
        return $statuses[$name];
    }
}
