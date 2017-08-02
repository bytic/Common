<?php

namespace ByTIC\Common\Records\Media\Covers;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Covers
 */
class Model extends \ByTIC\Common\Records\Media\Images\Model
{
    protected $mediaCollection = 'covers';

    /**
     * @param string $name
     */
    public function setName($name)
    {
        parent::setName($name);
        $this->url = $this->getModel()->getCoverURL($this->_type, $this->name);
        $this->path = $this->getModel()->getCoverPath($this->_type, $this->name);
    }
}
