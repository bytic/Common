<?php

namespace ByTIC\Common\Records\Media\Covers;

/**
 * Class Temp
 * @package ByTIC\Common\Records\Media\Covers
 * @deprecated use media library repo
 */
class Temp extends \ByTIC\Common\Records\Media\Images\Temp
{

    public function __construct()
    {
        parent::__construct();
        $this->max_width = 1170;
    }

}