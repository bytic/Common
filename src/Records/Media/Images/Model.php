<?php

namespace ByTIC\Common\Records\Media\Images;

use Nip\Records\Record;

class Model extends \Nip_File_Image
{

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

    public function getDefaultWidth()
    {
        $option = "images_" . $this->getModel()->getManager()->getTable() . "_" . $this->_type . "_width";
        return Options::instance()->$option;
    }

    public function getDefaultHeight()
    {
        $option = "images_" . $this->getManager()->getTable() . "_" . $this->_type . "_height";
        return Options::instance()->$option;
    }
//
//	public function resize($width = false, $height = false)
//	{
//		if (!$width) {
//			$width = $this->cropWidth;
//		}
//
//		if (!$height) {
//			$height = $this->cropHeight;
//		}
//
//		$image = imagecreatetruecolor($width, $height);
//		imagealphablending($image, false);
//		imagesavealpha($image, true);
//
//		imagecopyresampled($image, $this->_resource, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
//
//		$this->_width = $width;
//		$this->_height = $height;
//		$this->_resource = $image;
//
//		return $this;
//	}

    public function setModel(Record $model)
    {
        $this->_model = $model;
    }

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
        $this->url = $this->_model->getImageURL($this->_type, $this->name);
        $this->path = $this->_model->getImagePath($this->_type, $this->name);
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
            return $this->_model->deleteImage($this->name);
        }
    }

    public function __toString()
    {
        return $this->url;
    }

}