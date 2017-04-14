<?php

namespace ByTIC\Common\Records\Media\Files;

use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait;
use Nip\Filesystem\File;
use Nip\Filesystem\FileDisk;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Files
 *
 * @method FileDisk getFilesystem()
 */
class Model extends File
{
    /**
     * @var array
     */
    protected $extensions = [
        "swf", "pdf", "doc", "docx", "xls", "xlsx", "rtf", "ppt", "zip", "rar"
    ];

    /**
     * The model instance
     *
     * @var Record
     */
    protected $model;

    /**
     * Errors array for this file
     *
     * @var array
     */
    protected $errors;

    /**
     * Upload file from http
     *
     * @param UploadedFile $uploadedFile
     *
     * @return bool
     */
    public function upload($uploadedFile)
    {
        if ($uploadedFile->isValid()) {
            $this->getFilesystem()->putFileAs(
                dirname($this->getPath()),
                $uploadedFile,
                $this->getName()
            );

            return true;
        } else {
            $this->errors['upload'] = $uploadedFile->getErrorMessage();
        }

        return false;
    }

    /**
     * Get File path with init check
     *
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->initPath();
        }
        return parent::getPath();
    }

    /**
     * @return string
     */
    protected function initPath()
    {
        $this->setPath($this->getPathFolder() . $this->getName());
    }

    /**
     * @return string
     */
    public function getPathFolder()
    {
        return '/files/'
            . $this->getModelDirectoryName() . '/' . $this->getModel()->id . '/';
    }

    /**
     * @return string
     */
    public function getModelDirectoryName()
    {
        return $this->getModel()->getManager()->getTable();
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Record|RecordTrait $model
     * @return $this
     */
    public function setModel(Record $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param string|boolean $destination
     * @return bool
     */
    public function unzip($destination = false)
    {
        if (!$destination) {
            $destination = dirname($this->path);
        }

        $archive = new ZipArchive();

        if ($archive->open($this->path) === true) {
            $archive->extractTo($destination);
            $archive->close();

            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return 'file';
    }
}
