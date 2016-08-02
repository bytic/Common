<?php

namespace ByTIC\Common\Records\Media\Logos;

use Nip_File_System;

abstract class Model extends \ByTIC\Common\Records\Media\Images\Model {

    public $fHeight = false;
    public $fWidth = false;

    public function setName($name) {
        parent::setName($name);
        $this->url  = $this->getUrlPath() . $this->name;
        $this->path  = $this->getDirPath() . $this->name;
    }

    public function getDirRoot() {
        return dirname($this->getDirPath());
    }

    public function getDirPath() {
        return UPLOADS_PATH . $this->getRoutePath();
    }

    public function getUrlPath() {
        return UPLOADS_URL . $this->getRoutePath();
    }

    public function getUrl() {
        return $this->url ? $this->url : IMAGES_URL . $this->getDirectoryName() . '/' . $this->_type . '.png';
    }

    public function getPath() {
        return $this->path ? $this->path : IMAGES_PATH . $this->getDirectoryName() . '/' . $this->_type . '.png';
    }

    public function getRoutePath() {
        return 'images/' . $this->getDirectoryName() . '/' . $this->getModel()->id . '/logo-' . $this->_type . '/';
    }

    public function getDefaultName() {
        return 'logo';
    }

    public function getDirectoryName() {
        return $this->getModel()->getManager()->getController();
    }

    public function exists()
    {
        return is_file($this->getPath());
    }

    public function delete($bubble = false) {
        return Nip_File_System::instance()->removeDirectory($this->getDirPath());
    }

    public function validate() {
        return true;
    }

    public function  save() {
        if (is_dir($this->getDirPath())) {
            Nip_File_System::instance()->emptyDirectory($this->getDirPath());
        }

        $newName = $this->getDefaultName();
        $this->setBaseName($newName);
        $this->resize($this->fWidth, $this->fHeight);
        return parent::save();
    }

}