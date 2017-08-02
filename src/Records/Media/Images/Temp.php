<?php

namespace ByTIC\Common\Records\Media\Images;

/**
 * Class Temp
 * @package ByTIC\Common\Records\Media\Images
 */
class Temp extends \Nip_File_Image
{
    public $quality = 100;

    public function __construct()
    {
        $this->setBaseName(microtime(true));
        $this->path = $this->getBasePath() . $this->name;
        $this->url = $this->getBaseURL() . $this->name;
        $this->max_width = 1000;
    }

    public function getBasePath()
    {
        return UPLOADS_PATH . 'tmp/';
    }

    public function getBaseURL()
    {
        return UPLOADS_URL . 'tmp/';
    }

    public function validate()
    {
        return true;
    }
}
