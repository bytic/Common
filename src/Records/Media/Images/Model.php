<?php

namespace ByTIC\Common\Records\Media\Images;

use Nip\Records\Record as Record;
use Nip_File_Image;

class Model extends Nip_File_Image
{

    /**
     * @var Record
     */
    protected $_model;

    protected $_type;

    protected $_mediaType = 'images';

    public $basePath;
    public $baseURL;
    public $cropWidth;
    public $cropHeight;

    public function getSmall()
    {
        return $this->getType("small");
    }

    public function getMedium()
    {
        return $this->getType("medium");
    }

    public function getLarge()
    {
        return $this->getType("large");
    }

    public function getFull()
    {
        return $this->getType("full");
    }

    public function getType($type)
    {
        if ($type == $this->_type) {
            return $this;
        } else {
            $image = $this->_model->getNewImage($type);

            $image->setName($this->name);

            return $image;
        }
    }

    public function setResourceFromFile($path)
    {
        parent::setResourceFromFile($path);
        $this->setName(pathinfo($path, PATHINFO_BASENAME));

        return $this;
    }

    public function copyResource(Nip_File_Image $image)
    {
        parent::copyResource($image);
        $this->setName($image->name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultWidth()
    {
        $option = "images_" . $this->getModel()->getManager()->getTable() . "_" . $this->_type . "_width";
        return Options::instance()->$option;
    }

    /**
     * @return mixed
     */
    public function getDefaultHeight()
    {
        $option = "images_" . $this->getManager()->getTable() . "_" . $this->_type . "_height";
        return Options::instance()->$option;
    }

    /**
     * @param Record|\ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait $model
     */
    public function setModel(Record $model)
    {
        $this->_model = $model;
    }

    /**
     * @return Record
     */
    public function getModel()
    {
        return $this->_model;
    }

    public function getImageType()
    {
        return $this->_type;
    }

    public function setName($name)
    {
        parent::setName($name);
        $this->url = $this->getModel()->getImageURL($this->_type, $this->name);
        $this->path = $this->getModel()->getImagePath($this->_type, $this->name);
    }

    public function save()
    {
        $this->path = $this->path ? $this->path : $this->basePath . $this->name;
        parent::save();

        return $this;
    }

    public function delete($bubble = false)
    {
        if ($bubble) {
            return parent::delete();
        } else {
            return $this->getModel()->deleteImage($this->name);
        }
    }

    public function __toString()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getUploadRootPath()
    {
        return UPLOADS_PATH;
    }

    /**
     * @return mixed
     */
    public function getUploadRootURL()
    {
        return UPLOADS_URL;
    }

    /**
     * @return mixed
     */
    public function getImagesRootURL()
    {
        return IMAGES_URL;
    }

    /**
     * @return mixed
     */
    public function getImagesRootPath()
    {
        return IMAGES_PATH;
    }

}