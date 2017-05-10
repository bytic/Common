<?php

namespace ByTIC\Common\Records\Traits\Media\Generic;

use Nip\Filesystem\FileDisk;

/**
 * Trait RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Generic
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * Get the default files disk instance for current model
     *
     * @return FileDisk
     */
    public function getFilesystemDisk()
    {
        return app('filesystem')->disk($this->getFilesystemDiskName());
    }

    /**
     * @return string
     */
    public function getFilesystemDiskName()
    {
        return 'public';
    }

    /**
     * @param $request
     * @return array
     */
    protected function getCropCoordinates($request)
    {
        $return = [];

        $return['x'] = (int)$request['x'];
        $return['y'] = (int)$request['y'];
        $return['width'] = (int)$request['width'];
        $return['height'] = (int)$request['height'];

        return $return;
    }

}