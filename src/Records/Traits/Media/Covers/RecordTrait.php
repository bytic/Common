<?php

namespace ByTIC\Common\Records\Traits\Media\Covers;

use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as GenericMediaTrait;
use Nip_File_System;

trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;
    use GenericMediaTrait;

    public function getCovers()
    {
        if (!$this->getRegistry()->exists('covers')) {
            $covers = array();

            $image = $this->getNewCover($type);
            $files = Nip_File_System::instance()->scanDirectory($image->getDirPath());
            if ($files) {
                foreach ($files as $file) {
                    $newImage = $this->getNewCover($type);
                    $newImage->setName($file);

                    $covers[] = $newImage;
                }
            }

            $this->getRegistry()->set('covers', $covers);
        }

        return $this->getRegistry()->get('covers');
    }

    public function getCover()
    {
        $covers = $this->getCovers();
        $cover = is_array($covers) ? reset($covers) : null;

        if ($cover) {
            return $cover;
        }

        return $this->getGenericCover();
    }

    public function getGenericCover() {
        $image = $this->getNewCover();
        return $image;
    }

    public function getNewCover()
    {
        $class = $this->getCoverModelName();
        $image = new $class();
        $image->setModel($this);

        return $image;
    }

    public function getCoverModelName()
    {
        return $this->getManager()->getModel() . "_Cover";
    }

    public function uploadCover($file = false)
    {
        $image = $this->getNewCover();
        $file = $file ? $file : $_FILES['Filedata'];
        $uploadError = Nip_File_System::instance()->getUploadError($file, $image->extensions);

        if ($uploadError) {
            $this->errors['upload'] = 'Error Upload:' . $uploadError;
        } else {
            $image->setResourceFromUpload($file);
            if ($image->validate()) {
                if ($image->save()) {
                    return $image;
                }
                $this->errors['upload'] = 'Error saving file';
            } else {
                $error = is_array($image->errors) && count($image->errors) > 0 ? implode(', ', $image->errors) : 'Error validating file';
                $this->errors['upload'] = 'Error validate:' . $error;
            }
        }

        return false;
    }

    public function removeCover($request)
    {
        $request = is_array($request) ? $request : array();
        $image = $this->getNewCover();
        return $image->delete(true);
    }

}