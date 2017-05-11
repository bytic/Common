<?php

namespace ByTIC\Common\Records\Traits\Media\Generic;

use ByTIC\Common\Records\Media\Logos\Model as LogoModel;
use Nip\Filesystem\FileDisk;

/**
 * Trait RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Generic
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @param string $media
     * @param string $type
     * @return LogoModel
     */
    public function getNewMediaFile($media, $type = null)
    {
        $type = $this->checkMediaType($media, $type);
        $class = $this->getMediaModelName($media, $type);

        $logo = new $class();
        /** @var LogoModel $logo */
        $logo->setModel($this);
        $logo->setFilesystem($this->getFilesystemDisk());

        return $logo;
    }

    /**
     * @param $media
     * @param $type
     * @return mixed
     */
    public function checkMediaType($media, $type)
    {
        if (in_array($type, $this->getMediaTypes($media))) {
            return $type;
        }

        return $this->getGenericMediaType($media);
    }

    /**
     * @param $media
     * @return mixed
     */
    public function getMediaTypes($media)
    {
        return $this->{'get' . ucfirst($media) . 'Types'}();
    }

    /**
     * @param $media
     * @return mixed
     */
    public function getGenericMediaType($media)
    {
        return $this->{'getGeneric' . ucfirst($media) . 'Type'}();
    }

    /**
     * @param string $media
     * @param string|null $type
     * @return string
     */
    public function getMediaModelName($media, $type)
    {
        $type = $this->checkMediaType($media, $type);
        $type = inflector()->camelize($type);

        return $this->getManager()->getModel() . '_' . ucfirst($media) . '_' . $type;
    }

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