<?php

namespace ByTIC\Common\Records\Media\Traits;

/**
 * Trait HydratePath
 * @package ByTIC\Common\Records\Media\Traits
 *
 * @property $path
 */
trait HydratePath
{

    /**
     * Get file path folder
     *
     * @return string
     */
    public function getPathFolder()
    {
        return $this->getModel()->getFilesPath();
    }
}
