<?php

namespace ByTIC\Common\Records\Statuses;

use Records;
use ReflectionClass;

abstract class Generic
{
    public $name;
    public $label;

    protected $_item;

    public function  __construct()
    {
    }

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
     * @return \Records
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
    
    public function update()
    {
        $item = $this->getItem();
        if ($item) {
            $item->status = $this->getName();
            $this->preUpdate();
            $return = $item->save();
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

    public function getName()
    {
        if (!$this->name) {
            $name = (new ReflectionClass($this))->getShortName();
            $name = inflector()->unclassify($name);
            $this->name = $name;
        }
        return $this->name;
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
}