<?php

namespace ByTIC\Common\Records\Media\Traits;

/**
 * Trait HydrateCollection
 * @package ByTIC\Common\Records\Media\Traits
 *
 * @property $path
 *
 * @method  getModel
 */
trait HydrateCollection
{

    /**
     * Get file path folder
     *
     * @return string
     */
    public function getPathFolder()
    {
        return $this->getCollectionPathFolder();
    }

    /**
     * @return mixed
     */
    protected function getCollectionPathFolder()
    {
        $method = 'get' . $this->getMediaCollection() . 'Path';
        return $this->getModel()->{$method}();
    }

    /**
     * @return null|string
     */
    public function getMediaCollection()
    {
        return $this->mediaCollection;
    }
}
