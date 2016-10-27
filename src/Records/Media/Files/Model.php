<?php

namespace ByTIC\Common\Records\Media\Files;

use ByTIC\Common\Records\Traits\Media\Files\RecordTrait;
use Nip\Records\Record;
use Nip_File_System;
use ZipArchive;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Files
 */
class Model
{

    protected $_extensions = ["swf", "pdf", "doc", "docx", "xls", "xlsx", "rtf", "ppt", "zip", "rar"];

    /**
     * @var Record
     */
    protected $_model;

    protected $_name;
    protected $_path;
    protected $_url;

    protected $_size;

    protected $errors;

    public function upload($upload)
    {
        $error = Nip_File_System::instance()->getUploadError($upload, $this->getExtensions());
        if (!$error) {
            $this->setName($this->parseName($upload['name']));

            Nip_File_System::instance()->createDirectory(dirname($this->_path));

            if (!move_uploaded_file($upload["tmp_name"], $this->_path)) {
                return false;
            }

            chmod($this->_path, 0777);

            return true;
        } else {
            $this->errors['upload'] = $error;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function parseName($name)
    {
        return str_replace(' ', '-', $name);
    }

    public function unzip($dir = false)
    {
        if (!$dir) {
            $dir = dirname($this->_path);
        }

        $zip = new ZipArchive();

        if ($zip->open($this->_path) === true) {
            $zip->extractTo($dir);
            $zip->close();

            return true;
        }

        return false;
    }

    /**
     * @return $this
     */
    public function delete()
    {
        Nip_File_System::instance()->deleteFile($this->_path);
        return $this;
    }

    public function getEmbedURL()
    {
        $url = 'http://docs.google.com/gview';

        $params['url'] = $this->getUrl();
        $params['embedded'] = 'true';

        return $url . '?' . http_build_query($params);
    }

    public function getUrl()
    {
        return $this->_url ? $this->_url : $this->getUrlPath() . $this->_name;
    }

    public function getUrlPath()
    {
        return UPLOADS_URL . $this->getRoutePath();
    }

    public function getRoutePath()
    {
        return 'files/' . $this->getModel()->getManager()->getTable() . '/' . $this->getModel()->id . '/';
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param Record|RecordTrait $model
     * @return $this
     */
    public function setModel(Record $model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return Nip_File_System::instance()->getExtension($this->getPath());
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->_path ? $this->_path : $this->getDirPath() . $this->getName();
    }

    /**
     * @return string
     */
    public function getDirPath()
    {
        return UPLOADS_PATH . $this->getRoutePath();
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        $this->_path = $this->getPath();
        $this->_url = $this->getUrl();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        $fInfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($fInfo, $this->getPath());
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return filemtime($this->_path);
    }

    /**
     * Converts Bytes to human readable format
     *
     * @param int $precision
     * @return string
     */
    public function formatSize($precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($this->getSize(), 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return filesize($this->_path);
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
