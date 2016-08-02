<?php

namespace ByTIC\Common\Records\Traits\Media\Logos;

use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as GenericMediaTrait;
use Nip_File_System;

trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;
    use GenericMediaTrait;

    protected $logoTypes = null;

    public function getLogos($type = NULL)
    {
        if (!$this->getRegistry()->exists('logos')) {
            $logos = $this->initLogos();
            $this->getRegistry()->set('logos', $logos);
        }

        return $this->getRegistry()->get('logos');
    }

    public function initLogos()
    {
        $types = $this->getLogoTypes();
        $logos = array();
        foreach ($types as $type) {
            $image = $this->getNewLogo($type);
            $files = Nip_File_System::instance()->scanDirectory($image->getDirPath());
            if ($files) {
                foreach ($files as $file) {
                    $newImage = $this->getNewLogo($type);
                    $newImage->setName($file);

                    $logos[$type][] = $newImage;
                }
            } else {
//                    $logos[$type][] = $image;
            }
        }
        return $logos;
    }

    public function initLogoTypes()
    {
        $this->logoTypes = array();
    }

    public function getLogoTypes()
    {
        if ($this->logoTypes === null) {
            $this->initLogoTypes();
        }
        return $this->logoTypes;
    }

    public function getLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $logos = $this->getLogos();
        $logo = is_array($logos[$type]) ? reset($logos[$type]) : null;

        if ($logo) {
            return $logo;
        }

        return $this->getGenericLogo($type);
    }

    public function hasLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $logos = $this->getLogos();
        $logo = is_array($logos[$type]) ? reset($logos[$type]) : null;

        if ($logo) {
            return true;
        }

        return false;
    }

    public function getGenericLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $image = $this->getNewLogo($type);
        return $image;
    }

    /**
     * @return Manufacturer_Logos_Abstract
     */
    public function getNewLogo($type = NULL)
    {
        $type = $this->checkType($type);
        $class = $this->getLogoModelName($type);

        $image = new $class();
        $image->setModel($this);

        return $image;
    }

    public function getLogoModelName($type = NULL)
    {
        $type = $this->checkType($type);
        $type = inflector()->camelize($type);
        return $this->getManager()->getModel() . "_Logos_" . $type;
    }

    public function uploadLogo($type = null, $file = false)
    {
        $type = $this->checkType($type);

        $image = $this->getNewLogo($type);
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

    public function removeLogo($request)
    {
        $request = is_array($request) ? $request : array('type' => $request);
        $image = $this->getNewLogo($request['type']);
        return $image->delete(true);
    }

    public function checkType($type)
    {
        if (in_array($type, $this->getLogoTypes())) {
            return $type;
        }
        return $this->getGenericLogoType();
    }

    public function getGenericLogoType()
    {
        $types = $this->getLogoTypes();
        if (is_array($types)) {
            return reset($types);
        }
        return 'listing';
    }

}