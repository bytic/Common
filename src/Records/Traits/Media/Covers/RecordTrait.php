<?php

namespace ByTIC\Common\Records\Traits\Media\Covers;

use Nip_File_System;

trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_cover = [];
    protected $_coversCache = null;
    protected $_coverTypes = array('full', 'default');

    /**
     * Uploads cover to temporary directory
     *
     * @return mixed
     */
    public function uploadCover()
    {
        $cover = $this->getTempCover();
        $file = $_FILES['Filedata'];
        $uploadError = Nip_File_System::instance()->getUploadError($file, $cover->extensions);

        if (!$uploadError) {
            $cover->setResourceFromUpload($file);
            if ($cover->validate()) {
                $cover->resize();
                $cover->save();

                $coverCrop = $this->getTempCover();
                $coverCrop->copyResource($cover);

                $max_width = 600;
                $reductionRatio = 600 / $coverCrop->getWidth();

                $coverCrop->max_width = $max_width;
                $coverCrop->setName('crop-' . $cover->name);
                $coverCrop->resize();
                $coverCrop->save();

                $defaultCover = $this->getNewCover('default');
                $coverCrop->cropWidth = $defaultCover->cropWidth * $reductionRatio;
                $coverCrop->cropHeight = $defaultCover->cropHeight * $reductionRatio;

                return $coverCrop;
            } else {
                $this->errors['upload'] = 'Eroare dimensiuni.';
            }
        } else {
            $this->errors['upload'] = $uploadError;
        }


        return false;
    }

    /**
     * Uploads cover to temporary directory
     *
     * @return mixed
     */
    public function uploadCoverAutoCrop($file)
    {
        $cover = $this->getTempCover();
        $uploadError = Nip_File_System::instance()->getUploadError($file, $cover->extensions);

        if (!$uploadError) {
            $cover->setResourceFromUpload($file);
            if ($cover->validate()) {
                /** @var \Organization_Cover_Default $defaultCover */
                $defaultCover = $this->getNewCover('default');
                $defaultCover->copyResource($cover);
                $defaultCover->cropToCenter($defaultCover->cropWidth, $defaultCover->cropHeight);
                $defaultCover->save();
                return $defaultCover;
            } else {
                $this->errors['upload'] = 'Eroare dimensiuni.';
            }
        } else {
            $this->errors['upload'] = $uploadError;
        }
    }

    /**
     * @return Cover_Temp
     */
    public function getTempCover()
    {
        $class = get_class($this) . "_Cover_Temp";

        $cover = new $class();
        return $cover;
    }

    public function getCoverBasePath($type = false)
    {
        return $this->getUploadPath() . 'covers/' . $this->getManager()->getTable() . '/' . $this->id . '/' . ($type ? $type . '/' : '');
    }

    public function getCoverBaseURL($type = false)
    {
        return $this->getUploadURL() . 'covers/' . $this->getManager()->getTable() . '/' . $this->id . '/' . ($type ? $type . '/' : '');
    }

    public function getCoverPath($type, $cover)
    {
        return $this->getCoverBasePath($type) . $cover;
    }

    public function getCoverURL($type, $cover)
    {
        return $this->getCoverBaseURL($type) . $cover;
    }

    public function getNewCover($type)
    {
        $class = get_class($this) . "_Cover_" . ucfirst($type);

        $cover = new $class();

        $cover->basePath = $this->getCoverBasePath($type);
        $cover->baseURL = $this->getCoverBaseURL($type);

        $cover->setModel($this);

        return $cover;
    }

    public function getNewCovers()
    {
        $return = array();

        foreach ($this->_coverTypes as $type) {
            $return[$type] = $this->getNewCover($type);
        }

        return $return;
    }

    public function checkCovers()
    {
        if ($this->_coversCache === null) {
            $this->initCovers();
        }
    }

    public function initCovers()
    {
        foreach ($this->_coverTypes as $type) {
            $this->initCoversByType($type);
        }
    }

    public function getCovers($type = "default")
    {
        if (!$this->_coversCache[$type]) {
            $this->initCoversByType($type);
        }
        return $this->_coversCache[$type];
    }

    public function initCoversByType($type = "default")
    {
        $files = Nip_File_System::instance()->scanDirectory($this->getCoverBasePath($type));
        $return = array();
        foreach ($files as $file) {
            $cover = $this->getNewCover($type);
            $cover->setName($file);

            $return[$cover->name] = $cover;
        }

        $this->_coversCache[$type] = $return;
    }

    public function findCover($type = "default")
    {
        $this->findCovers($type);

        if ($this->covers) {
        }
    }

    public function getCover($type = "default")
    {
        if (!isset($this->cover[$type])) {
            $this->initCover($type);
        }

        return $this->cover[$type];
    }

    public function initCover($type = "default")
    {
        $this->checkCovers();
        $covers = $this->getCovers($type);

        if (count($covers)) {
            if ($this->default_cover && $covers[$this->default_cover]) {
                $cover = $covers[$this->default_cover];
            } else {
                $cover = reset($covers);
                $this->default_cover = $this->cover->name;
                //                $this->update();
            }
        } else {
            $cover = $this->getNewCover($type);
        }

        $this->cover[$type] = $cover;
    }

    public function hasCover($type = "default")
    {
        $this->checkCovers();
        return isset($this->_coversCache[$type]) && count($this->_coversCache[$type]) > 0;
    }

    public function deleteCovers()
    {
        return Nip_File_System::instance()->removeDir($this->getCoverBasePath());
    }

    public function resetCovers()
    {
        return Nip_File_System::instance()->emptyDirectory($this->getCoverBasePath());
    }

    public function deleteCover($name)
    {
        $covers = $this->getNewCovers();

        foreach ($covers as $cover) {
            $cover->setName($name);
            $cover->delete(true);
        }
    }

    /**
     * Saves cropped covers
     *
     * @param array $request
     */
    public function cropCovers($request)
    {
        $path = $request['path'];
        $coords = $this->getCropCoordinates($request);
        $covers = $this->getNewCovers();

        $full = reset($this->_coverTypes);

        $cropperCover = $this->getTempCover();
        $cropperCover->setResourceFromFile(UPLOADS_PATH . 'tmp/' . $path);

        $originalCover = $this->getTempCover();
        $originalCover->setResourceFromFile(UPLOADS_PATH . 'tmp/' . str_replace('crop-', '', $path));

        $covers[$full]->setResourceFromFile($originalCover->getFile());
        $covers[$full]->save();

        $crop = next($this->_coverTypes);
        $workingCover = $covers[$crop];
        $workingCover->copyResource($covers[$full]);

        $ratio = $covers[$full]->getWidth() / $cropperCover->getWidth();
        $adjustX = round($coords['x'] * $ratio);
        $adjustY = round($coords['y'] * $ratio);
        $adjustWidth = round($coords['width'] * $ratio);
        $adjustHeight = round($coords['height'] * $ratio);

        $workingCover->crop(
            $adjustX, $adjustY,
            $workingCover->cropWidth, $workingCover->cropHeight,
            $adjustWidth, $adjustHeight);

        while ($size = next($this->_coverTypes)) {
            $covers[$size]->copyResource($covers[$crop])->resize()->unsharpMask()->save();
        }

        $covers[$crop]->unsharpMask()->save();

        Nip_File_System::instance()->deleteFile($cropperCover->getFile());
        Nip_File_System::instance()->deleteFile($originalCover->getFile());

        return $covers['default'];
    }

    public function setDefaultCover($request)
    {
        $cover = $request['cover'];
        $files = Nip_File_System::instance()->scanDirectory($this->getCoverBasePath(), true);

        if (!in_array($cover, $files)) {
            return false;
        }

        $this->default_cover = $cover;
        $this->update();

        return true;
    }

    public function removeCover($request)
    {
        foreach ($this->_coverTypes as $type) {
            $covers = $this->getCovers($type);

            if ($covers[$request['image']]) {
                $this->deleteCover($request['image']);
            }
        }

        return true;
    }

    public function isDefaultCover($cover)
    {
        if ($cover instanceof Nip_File_Image) {
            $cover = $cover->name;
        }

        return $this->default_cover == $cover;
    }
}
