<?php

namespace ByTIC\Common\Records\Traits\Media\Generic;

trait RecordTrait
{

    public function getUploadPath()
    {
        return UPLOADS_PATH;
    }

    public function getUploadURL()
    {
        return UPLOADS_URL;
    }

    public function getImageBasePath($type = false)
    {
        return $this->getUploadPath() . 'images/' . $this->getManager()->getTable() . '/' . $this->id . '/' . ($type ? $type . '/' : '');
    }

    public function getImageBaseURL($type = false)
    {
        return $this->getUploadURL() . 'images/' . $this->getManager()->getTable() . '/' . $this->id . '/' . ($type ? $type . '/' : '');
    }

    public function getImageURL($type, $image)
    {
        return $this->getImageBaseURL($type) . $image;
    }

    public function getImagePath($type, $image)
    {
        return $this->getImageBasePath($type) . $image;
    }
    
}