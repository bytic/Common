<?php

namespace ByTIC\Common\Records\Traits\Media\Images;

use ByTIC\Common\Records\Media\Images\Temp as ImageTemp;
use Nip_File_System;

/**
 * Trait RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Images
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\Media\Generic\RecordTrait;

    public $images = [];

    protected $_imageCache = [];
    protected $_imageTypes = ['full', 'default'];

    /**
     * Uploads image to temporary directory
     *
     * @return mixed
     */
    public function uploadImage()
    {
        $image = $this->getTempImage();
        $file = $_FILES['Filedata'];
        $uploadError = Nip_File_System::instance()->getUploadError($file, $image->extensions);

        if (!$uploadError) {
            $image->setResourceFromUpload($file);
            if ($image->validate()) {
                $image->resize();
                $image->save();

                $imageCrop = $this->getTempImage();
                $imageCrop->copyResource($image);

                $ratio = $imageCrop->getRatio();
                $max_width = $imageCrop->minWidth > 600 ? $imageCrop->minWidth : 600;
                $heightCalculated = round($max_width / $ratio);
                if ($heightCalculated < $imageCrop->minHeight) {
                    $max_width = round($imageCrop->minHeight * $ratio);
                }

                $imageCrop->max_width = $max_width;
                $imageCrop->setName('crop-' . $image->name);
                $imageCrop->resize();
                $imageCrop->save();

                return $imageCrop;
            } else {
                $this->errors['upload'] = 'Eroare dimensiuni.';
            }
        } else {
            $this->errors['upload'] = $uploadError;
        }


        return false;
    }

    /**
     * @return ImageTemp
     */
    public function getTempImage()
    {
        $class = get_class($this) . "_ImageTemp";

        $image = new $class();
        return $image;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getImageRatio($type = null)
    {
        return number_format(($this->getImageWidth($type) / $this->getImageHeight($type)), 2, '.', '');
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getImageWidth($type = null)
    {
        $image = $this->getImageByType($type);
        return $image->cropWidth;
    }

    /**
     * @param null $type
     * @return ImageTemp|mixed
     */
    public function getImageByType($type = null)
    {
        return in_array($type, $this->getImageTypes()) ? $this->getNewImage($type) : $this->getTempImage();
    }

    /**
     * @return array
     */
    public function getImageTypes()
    {
        return $this->_imageTypes;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getNewImage($type)
    {
        return $this->getNewMediaFile('image', $type);
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getImageHeight($type = null)
    {
        $image = $this->getImageByType($type);
        return $image->cropHeight;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getImage($type = "default")
    {
        $this->findImage($type);

        if ($this->image) {
            return $this->image->url;
        }

        return $this->getGenericImage($type);
    }

    /**
     * @param string $type
     */
    public function findImage($type = "default")
    {
        $this->findImages($type);

        if ($this->images) {
            if ($this->default_image && $this->images[$this->default_image]) {
                $this->image = $this->images[$this->default_image];
            } else {
                $this->image = reset($this->images);
                $this->default_image = $this->image->name;
                if (in_array('default_image', $this->getManager()->getFields())) {
                    $this->update();
                }
            }
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public function findImages($type = "default")
    {
        if (!$this->_imageCache[$type]) {
            $return = [];

            $image = $this->getNewImage($type);

            $files = $image->getFilesystem()->listContents(
                $image->getPathFolder()
            );

            foreach ($files as $file) {
                $image = $this->getNewImage($type);
                $image->setName($file);

                if ($file == $this->default_image) {
                    $return = [$image->name => $image] + $return;
                } else {
                    $return[$image->name] = $image;
                }
            }

            $this->_imageCache[$type] = $return;
        }
        $this->images = $this->_imageCache[$type];

        return $this->images;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getGenericImage($type = "default")
    {
        return asset('images/' . $this->getManager()->getTable() . '/default-' . $type . '.jpg');
    }

    /**
     * @return mixed
     */
    public function deleteImages()
    {
        return Nip_File_System::instance()->removeDir($this->getImageBasePath());
    }

    /**
     * @return $this
     */
    public function resetImages()
    {
        return Nip_File_System::instance()->emptyDirectory($this->getImageBasePath());
    }

    /**
     * @param $name
     */
    public function deleteImage($name)
    {
        $images = $this->getNewImages();

        foreach ($images as $image) {
            $image->setName($name);
            $image->delete(true);
        }
    }

    /**
     * @return array
     */
    public function getNewImages()
    {
        $return = [];

        foreach ($this->_imageTypes as $type) {
            $return[$type] = $this->getNewImage($type);
        }

        return $return;
    }

    /**
     * Saves cropped images
     *
     * @param array $request
     */
    public function cropImages($request)
    {
        $path = $request['path'];
        $coords = $this->getCropCoordinates($request);
        $images = $this->getNewImages();

        $full = reset($this->_imageTypes);

        $cropperImage = $this->getTempImage();
        $cropperImage->setResourceFromFile(UPLOADS_PATH . 'tmp/' . $path);

        $originalImage = $this->getTempImage();
        $originalImage->setResourceFromFile(UPLOADS_PATH . 'tmp/' . str_replace('crop-', '', $path));

        $images[$full]->setResourceFromFile($originalImage->getFile());
        $images[$full]->save();

        $crop = next($this->_imageTypes);
        $images[$crop]->copyResource($images[$full]);

        $ratio = $images[$full]->getWidth() / $cropperImage->getWidth();
        $adjustX = round($coords['x'] * $ratio);
        $adjustY = round($coords['y'] * $ratio);
        $adjustWidth = round($coords['width'] * $ratio);
        $adjustHeight = round($coords['height'] * $ratio);

        $images[$crop]->crop(
            $adjustX, $adjustY,
            $this->getImageWidth($crop), $this->getImageHeight($crop),
            $adjustWidth, $adjustHeight);

        while ($size = next($this->_imageTypes)) {
            $images[$size]->copyResource($images[$crop])->resize()->unsharpMask()->save();
        }

        $images[$crop]->unsharpMask()->save();

        Nip_File_System::instance()->deleteFile($cropperImage->getFile());
        Nip_File_System::instance()->deleteFile($originalImage->getFile());

        return $images['default'];
    }

    /**
     * @param $request
     * @return bool
     */
    public function setDefaultImage($request)
    {
        $image = $request['image'];
        $files = Nip_File_System::instance()->scanDirectory($this->getImageBasePath(), true);

        if (!in_array($image, $files)) {
            return false;
        }

        $this->default_image = $image;
        $this->update();

        return true;
    }

    /**
     * @param $request
     * @return bool
     */
    public function removeImage($request)
    {
        foreach ($this->_imageTypes as $type) {
            $this->findImages($type);

            if ($this->images[$request['image']]) {
                $this->images[$request['image']]->delete();
            }
        }

        return true;
    }

    /**
     * @param $image
     * @return bool
     */
    public function isDefaultImage($image)
    {
        if ($image instanceof Nip_File_Image) {
            $image = $image->name;
        }

        return $this->default_image == $image;
    }
}
