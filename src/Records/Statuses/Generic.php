<?php

namespace ByTIC\Common\Records\Statuses;

use Nip\Records\RecordManager as Records;
use Nip\Records\Record as Record;
use ReflectionClass;

abstract class Generic
{
    public $name = null;

    public $label = null;
    public $label_short = null;

    /**
     * @var null|Record
     */
    protected $_item;

    /**
     * @var null|Records
     */
    protected $_manager;


    public function setItem($Item)
    {
        $this->_item = $Item;
        return $this;
    }

    public function getItem()
    {
        return $this->_item;
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        return $this->_manager;
    }

    public function setManager(Records $manager)
    {
        $this->_manager = $manager;
        $this->getName();
        $this->getLabel();
        return $this;
    }


    public function getName()
    {
        if ($this->name == null) {
            $this->initName();
        }
        return $this->name;
    }

    public function initName()
    {
        $this->name = $this->generateName();
    }

    public function generateName()
    {
        $name = (new ReflectionClass($this))->getShortName();
        $name = inflector()->unclassify($name);
        return $name;
    }

    public function getLabel($short = false)
    {
        if (!$this->label) {
            $this->label = $this->getManager()->translate('statuses.' . $this->getName());
            $this->label_short = $this->getManager()->translate('statuses.' . $this->getName() . '.short');
        }
        return $short ? $this->label_short : $this->label;
    }

    public function getLabelHTML($short = false)
    {
        return '<span class="label label-'.$this->getColorClass().'" rel="tooltip" title="'.$this->getLabel().'"  style="font-size:100%;'.$this->getColorCSS().'">
            '.$this->getLabel($short).'
        </span>';
    }

    public function getColorClass()
    {
        return 'default';
    }

    public function getColorCSS()
    {
        return 'background-color: '.$this->getBGColor().';color:'.$this->getFGColor().';';
    }

    public function getBGColor()
    {
        return '#999';
    }

    public function getFGColor()
    {
        return '#fff';
    }

    public function update()
    {
        $item = $this->getItem();
        if ($item) {
            $item->status = $this->getName();
            $this->preUpdate();
            $return = $item->saveRecord();
            $this->postUpdate();
            return $return;
        }
        return false;
    }

    public function  preUpdate()
    {
    }

    public function  postUpdate()
    {
    }
}