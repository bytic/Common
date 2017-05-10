<?php

namespace ByTIC\Common\Records\Media\Logos;

use Nip_File_System;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Logos
 *
 * @method \ByTIC\Common\Records\Traits\Media\Logos\RecordTrait getModel()
 *
 */
abstract class Model extends \ByTIC\Common\Records\Media\Images\Model
{
    public $fHeight = false;
    public $fWidth = false;

    public function setName($name)
    {
        parent::setName($name);
        $this->url = $this->getUrlPath() . $this->name;
        $this->path = $this->getDirPath() . $this->name;
    }

    public function getUrl()
    {
        if (!$this->url) {
            $this->initUrl();
        }
        return $this->url;
    }

    public function initUrl()
    {
        $this->url = $this->getImagesRootURL() . $this->getDirectoryName() . '/' . $this->_type . '.png';
    }

    /**
     * Get file path folder
     *
     * @return string
     */
    public function getPathFolder()
    {
        return $this->getModel()->getLogosPath() . '/logo-' . $this->_type . '/';
    }

    public function validate()
    {
        return true;
    }

    public function save()
    {
        if (is_dir($this->getDirPath())) {
            Nip_File_System::instance()->emptyDirectory($this->getDirPath());
        }

        $newName = $this->getDefaultName();
        $this->setBaseName($newName);
        $this->processSize();
        return parent::save();
    }

    public function getDefaultName()
    {
        return 'logo';
    }

    public function processSize()
    {
        $this->resize($this->fWidth, $this->fHeight);
    }

}