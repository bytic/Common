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
    use \ByTIC\Common\Records\Traits\HasSmartProperties\RecordsTrait;

    /**
     * @param $name
     * @return array
     */
    public function getStatusProperty($name)
    {
        return $this->getSmartPropertyValues('Status', $name);
    }

    /**
     * @return null|GenericStatus[]
     */
    public function getStatuses()
    {
        return $this->getSmartPropertyItems('Status');
    }

    /**
     * @return null|string
     */
    public function getStatusesDirectory()
    {
        return $this->getSmartPropertyDefinition('Status')->getItemsDirectory();
    }

    /**
     * @param string $name
     * @return GenericStatus
     * @throws Exception
     */
    public function getStatus($name = null)
    {
        return $this->getSmartPropertyItem('Status', $name);
    }

    protected function registerSmartProperties()
    {
        $this->registerSmartProperty('status');
    }
}
