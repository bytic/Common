<?php

namespace ByTIC\Common\Records\Traits\Media\Files;

use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as GenericMediaTrait;
use Nip_File_System;

trait RecordTrait
{
    use GenericMediaTrait;

    public $files = array();

    public function getFileURL($file)
    {
        return $this->getUploadURL() . $file->getName();
    }

    /**
     * File factory
     * @return Model_File
     */
    public function getNewFile()
    {
        $class = $this->getFileModelName();
        $file = new $class();

        $file->setModel($this);
        return $file;
    }

    public function getFileModelName()
    {
        return $this->getManager()->getModel() . "_File";
    }

    public function uploadFile($fileData)
    {
        $file = $this->getNewFile();

        if ($file->upload($fileData)) {
            return $file;
        }

        return false;
    }

    public function findFiles()
    {
        $file = $this->getNewFile();
        $files = Nip_File_System::instance()->scanDirectory($file->getDirPath());
        natsort($files);
        $this->setFiles($files);

        return $this->files;
    }

    public function setFiles($files = array())
    {
        if ($files) {
            foreach ($files as $name) {
                $file = $this->getNewFile();
                $file->setName($name);

                $this->files[$name] = $file;
            }
        }
    }

    public function removeFile($request)
    {
        $this->findFiles();

        if ($this->files[$request['file']]) {
            $this->files[$request['file']]->delete();
        }

        return true;
    }
}