<?php

namespace ByTIC\Common\Records\Traits\Media\Logos;

use ByTIC\Common\Records\Media\Logos\Model as LogoModel;
use Nip_File_System;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Logos
 *
 * @property array $errors
 *
 */
trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_logoTypes = [];

    /**
     * @param string|null $type
     * @return mixed
     */
    public function getLogos($type = NULL)
    {
        if (!$this->getRegistry()->exists('logos')) {
            $this->initLogos();
        }

        $type = $this->checkType($type);
        $logos = $this->getRegistry()->get('logos');
        return $logos[$type];
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
        $this->getRegistry()->set('logos', $logos);
    }

    public function initLogoTypes()
    {
        if (isset($this->logoTypes)) {
            $this->_logoTypes = $this->logoTypes;
        } else {
            $this->_logoTypes = array();
        }
    }

    public function getLogoTypes()
    {
        if (count($this->_logoTypes) < 1) {
            $this->initLogoTypes();
        }
        return $this->_logoTypes;
    }

    /**
     * @param string|null $type
     * @return LogoModel
     */
    public function getLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $logos = $this->getLogos($type);
        $logo = is_array($logos) ? reset($logos) : null;

        if ($logo) {
            return $logo;
        }

        return $this->getGenericLogo($type);
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function hasLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $logos = $this->getLogos($type);

        if (is_array($logos[$type])) {
            return true;
        }

        return false;
    }

    /**
     * @param string|null $type
     * @return LogoModel
     */
    public function getGenericLogo($type = NULL)
    {
        $type = $this->checkType($type);

        $image = $this->getNewLogo($type);
        return $image;
    }

    /**
     * @param string $type
     * @return LogoModel
     */
    public function getNewLogo($type = NULL)
    {
        $type = $this->checkType($type);
        $class = $this->getLogoModelName($type);

        $logo = new $class();
        /** @var LogoModel $logo */
        $logo->setModel($this);

        return $logo;
    }

    /**
     * @param string|null $type
     * @return string
     */
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