<?php

namespace ByTIC\Common\Records\Properties\AbstractProperty;

use ByTIC\Common\Records\Properties\AbstractProperty\Traits\HasManagerTrait;
use Nip\Records\Record as Record;
use ReflectionClass;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Properties\AbstractProperty
 */
abstract class Generic
{
    use HasManagerTrait;

    protected $name = null;

    protected $label = null;

    protected $label_short = null;

    /**
     * @var null|Record
     */
    protected $item;

    /**
     * @var null|string
     */
    protected $field;

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return null;
    }

    /**
     * @param bool $short
     * @return string
     */
    public function getLabelHTML($short = false)
    {
        return '<span class="'.$this->getLabelClasses().'" rel="tooltip" title="'.$this->getLabel().'"  
        style="'.$this->getColorCSS().'">
            '.$this->getIconHTML().'
            '.$this->getLabel($short).'
        </span>';
    }

    /**
     * get the label class name
     *
     * @return string
     */
    public function getLabelClasses()
    {
        return 'label label-'.$this->getColorClass();
    }

    /**
     * Get the color class
     *
     * @return string
     */
    public function getColorClass()
    {
        return 'default';
    }

    /**
     * Get Property label
     *
     * @param bool $short short flag
     *
     * @return null
     */
    public function getLabel($short = false)
    {
        if (!$this->label) {
            $this->label = $this->generateLabel();
            if ($this->hasShortLabel()) {
                $this->label_short = $this->generateLabelShort();
            }
        }

        return $short ? $this->label_short : $this->label;
    }

    /**
     * @return string
     */
    protected function generateLabel()
    {
        return $this->getManager()->translate($this->getLabelSlug().'.'.$this->getName());
    }

    /**
     * @return string
     */
    abstract protected function getLabelSlug();

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
        $name = $this->generateNameFromClass();
        $name = inflector()->unclassify($name);

        return $name;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function generateNameFromClass()
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * @return boolean
     */
    protected function hasShortLabel()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function generateLabelShort()
    {
        return $this->getManager()->translate($this->getLabelSlug().'.'.$this->getName().'.short');
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
        if ($this->getFGColor()) {
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
     * @return string
     */
    public function getIconHTML()
    {
        $icon = $this->getIcon();
        $return = '';
        if ($icon) {
            $return .= '<span class="glyphicon glyphicon-white '.$icon.'"></span> ';
        }

        return $return;
    }

    /**
     * @return bool|string
     */
    public function getIcon()
    {
        return false;
    }

    /**
     * @return bool|mixed
     */
    public function update()
    {
        $item = $this->getItem();
        if ($item) {
            $this->preValueChange();
            /** @noinspection PhpUndefinedFieldInspection */
            $item->{$this->getField()} = $this->getName();
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

    public function preValueChange()
    {
    }

    /**
     * @return null|string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param null|string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return 'info';
    }
}
