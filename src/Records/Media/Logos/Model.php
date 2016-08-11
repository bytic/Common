<?php

namespace ByTIC\Common\Records\Media\Logos;

use Nip_File_System;

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

    public function getDirRoot()
    {
        return dirname($this->getDirPath());
    }

    public function getDirPath()
    {
        return $this->getUploadRootPath() . $this->getRoutePath();
    }

    public function getUrlPath()
    {
        return $this->getUploadRootURL() . $this->getRoutePath();
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

    public function getPath()
    {
        if (!$this->path) {
            $this->initPath();
        }
        return $this->path;
    }

    public function initPath()
    {
        $this->path = $this->getImagesRootPath() . $this->getDirectoryName() . '/' . $this->_type . '.png';
    }

    public function getRoutePath()
    {
        return 'images/' . $this->getDirectoryName() . '/' . $this->getModel()->id . '/logo-' . $this->_type . '/';
    }

    public function getDefaultName()
    {
        return 'logo';
    }

    public function getDirectoryName()
    {
        return $this->getModel()->getManager()->getController();
    }

    public function exists()
    {
        return is_file($this->getPath());
    }

    public function delete($bubble = false)
    {
        return Nip_File_System::instance()->removeDirectory($this->getDirPath());
    }

    public function validate()
    {
        return true;
    }

    public function  save() {
        if (is_dir($this->getDirPath())) {
            Nip_File_System::instance()->emptyDirectory($this->getDirPath());
        }

        $newName = $this->getDefaultName();
        $this->setBaseName($newName);
        $this->processSize();
        return parent::save();
    }

    public function processSize()
    {
        $this->resize($this->fWidth, $this->fHeight);
    }

}