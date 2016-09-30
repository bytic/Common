<?php

namespace ByTIC\Common\Records\Types;

use Records;
use ReflectionClass;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Types
 */
abstract class Generic
{
    public $name;
    public $label;

    protected $item;

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($Item)
    {
        $this->item = $Item;
        return $this;
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
            $this->label = $this->getManager()->getLabel('types.' . $this->getName());
            $this->label_short = $this->getManager()->getLabel('types.' . $this->getName() . '.short');
        }
        return $short ? $this->label_short : $this->label;
    }

    public function getManager()
    {
        return $this->_manager;
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