<?php

namespace ByTIC\Common\Records\Statuses;

use ByTIC\Common\Records\Traits\I18n\RecordsTrait as RecordsTranslated;
use Nip\Records\Record as Record;
use Nip\Records\RecordManager as Records;
use ReflectionClass;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Statuses
 */
abstract class Generic
{
    public $name = null;

    public $label = null;
    public $label_short = null;

    /**
     * @var null|Record
     */
    protected $item;

    /**
     * @var null|Records
     */
    protected $manager;

    /**
     * @return Records|RecordsTranslated
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Records $manager
     * @return $this
     */
    public function setManager(Records $manager)
    {
        $this->manager = $manager;
        $this->getName();
        $this->getLabel();

        return $this;
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function generateName()
    {
        $name = (new ReflectionClass($this))->getShortName();
        $name = inflector()->unclassify($name);

        return $name;
    }

    /**
     * @param bool $short
     * @return null
     */
    public function getLabel($short = false)
    {
        if (!$this->label) {
            $this->label = $this->getManager()->translate('statuses.'.$this->getName());
            $this->label_short = $this->getManager()->translate('statuses.'.$this->getName().'.short');
        }

        return $short ? $this->label_short : $this->label;
    }

    /**
     * @param bool $short
     * @return string
     */
    public function getLabelHTML($short = false)
    {
        return '<span class="'.$this->getLabelClasses().'" rel="tooltip" title="'.$this->getLabel().'"  
        style="'.$this->getColorCSS().'">
            '.$this->getLabel($short).'
        </span>';
    }

    /**
     * @return string
     */
    public function getLabelClasses()
    {
        return 'label label-'.$this->getColorClass();
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return 'default';
    }

    /**
     * @return string
     */
    public function getColorCSS()
    {
        $css = [];
        if ($this->getBGColor()) {
            $css[] = 'background-color: '.$this->getBGColor();
        }
        if ($this->getBGColor()) {
            $css[] = 'color: '.$this->getFGColor();
        }

        return implode(';', $css);
    }

    /**
     * @return bool|string
     */
    public function getBGColor()
    {
        return false;
    }

    /**
     * @return bool|string
     */
    public function getFGColor()
    {
        return false;
    }

    /**
     * @return bool|void
     */
    public function update()
    {
        $item = $this->getItem();
        if ($item) {
            /** @noinspection PhpUndefinedFieldInspection */
            $item->status = $this->getName();
            $this->preUpdate();
            $return = $item->saveRecord();
            $this->postUpdate();

            return $return;
        }

        return false;
    }

    /**
     * @return Record|null
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param $i
     * @return $this
     */
    public function setItem($i)
    {
        $this->item = $i;

        return $this;
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }
}
