<?php

namespace ByTIC\Common\Records\Traits\Media\Files;

use ByTIC\Common\Records\Media\Files\Model as ModelFile;
use Nip_File_System;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Files
 */
trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @var ModelFile[]
     */
    public $files = [];

    /**
     * @param ModelFile $file
     * @return string
     */
    public function getFileURL($file)
    {
        return $this->getUploadURL() . $file->getName();
    }

    /**
     * @param $fileData
     * @return bool|ModelFile
     */
    public function uploadFile($fileData)
    {
        $file = $this->getNewFile();

        if ($file->upload($fileData)) {
            return $file;
        }

        return false;
    }

    /**
     * File factory
     * @return ModelFile
     */
    public function getNewFile()
    {
        $class = $this->getFileModelName();
        /** @var ModelFile $file */
        $file = new $class();

        $file->setModel($this);
        return $file;
    }

    /**
     * @return string
     */
    public function getFileModelName()
    {
        return $this->getManager()->getModel() . "_File";
    }

    /**
     * @param $request
     * @return bool
     */
    public function removeFile($request)
    {
        $this->findFiles();

        if ($this->files[$request['file']]) {
            $this->files[$request['file']]->delete();
        }

        return true;
    }

    /**
     * @return array
     */
    public function findFiles()
    {
        $files = Nip_File_System::instance()->scanDirectory($this->getFilesDirectory());
        natsort($files);
        $this->setFiles($files);

        return $this->files;
    }

    /**
     * @return string
     */
    public function getFilesDirectory()
    {
        $file = $this->getNewFile();

        return $file->getDirPath();
    }

    /**
     * @param array $files
     */
    public function setFiles($files = [])
    {
        if ($files) {
            foreach ($files as $name) {
                $file = $this->getNewFile();
                $file->setName($name);

                $this->files[$name] = $file;
            }
        }
    }
}
