<?php

namespace ByTIC\Common\Records\Media\Covers;

class Temp extends \ByTIC\Common\Records\Media\Images\Temp
{
    public function __construct()
    {
        parent::__construct();
        $this->max_width = 1170;
    }
}
