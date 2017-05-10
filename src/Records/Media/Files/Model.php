<?php

namespace ByTIC\Common\Records\Media\Files;

use Nip\Filesystem\File;
use Nip\Filesystem\FileDisk;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

/**
 * Class Model
 *
 * @package ByTIC\Common\Records\Media\Files
 *
 * @method FileDisk getFilesystem()
 */
class Model extends File
{
    use \ByTIC\Common\Records\Media\Traits\HasModels;
    use \ByTIC\Common\Records\Media\Traits\HydratePath;

    /**
     * @var array
     */
    protected $extensions = [
        "swf", "pdf", "doc", "docx", "xls", "xlsx", "rtf", "ppt", "zip", "rar"
    ];

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
     * Get valid extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Unzip file
     *
     * @param string|boolean $destination Destination folder
     *
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
     * Get errors array
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get default name
     *
     * @return string
     */
    public function getDefaultName()
    {
        return 'file';
    }
}
