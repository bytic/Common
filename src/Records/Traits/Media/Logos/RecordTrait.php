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
     * @param string|null $type Type name string
     * @return LogoModel
     */
    public function getLogo($type = null)
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
     * @param $type
     * @return mixed|string
     */
    public function checkType($type)
    {
        if (in_array($type, $this->getLogoTypes())) {
            return $type;
        }

        return $this->getGenericLogoType();
    }

    /**
     * @return array
     */
    public function getLogoTypes()
    {
        if (count($this->_logoTypes) < 1) {
            $this->initLogoTypes();
        }

        return $this->_logoTypes;
    }

    /**
     * Init Logo Types
     */
    public function initLogoTypes()
    {
        if (isset($this->logoTypes)) {
            $this->_logoTypes = $this->logoTypes;
        } else {
            $this->_logoTypes = [];
        }
    }

    /**
     * @return mixed|string
     */
    public function getGenericLogoType()
    {
        $types = $this->getLogoTypes();
        if (is_array($types)) {
            return reset($types);
        }

        return 'listing';
    }

    /**
     * @param string|null $type
     * @return mixed
     */
    public function getLogos($type = null)
    {
        if (!$this->getRegistry()->has('logos')) {
            $this->initLogos();
        }

        $type = $this->checkType($type);
        $logos = $this->getRegistry()->get('logos');

        return $logos[$type];
    }

    public function initLogos()
    {
        $types = $this->getLogoTypes();
        $logos = [];
        foreach ($types as $type) {
            $image = $this->getNewLogo($type);
            $files = Nip_File_System::instance()->scanDirectory(
                $image->getRealPath()
            );
            if ($files) {
                foreach ($files as $file) {
                    $newImage = $this->getNewLogo($type);
                    $newImage->setName($file);

                    $logos[$type][] = $newImage;
                }
            } else {
                $logos[$type][] = $image;
            }
        }
        $this->getRegistry()->set('logos', $logos);
    }

    /**
     * @param string $type
     * @return LogoModel
     */
    public function getNewLogo($type = null)
    {
        $type = $this->checkType($type);
        $class = $this->getLogoModelName($type);

        $logo = new $class();
        /** @var LogoModel $logo */
        $logo->setModel($this);
        $logo->setFilesystem($this->getFilesystemDisk());

        return $logo;
    }

    /**
     * @param string|null $type
     * @return string
     */
    public function getLogoModelName($type = null)
    {
        $type = $this->checkType($type);
        $type = inflector()->camelize($type);

        return $this->getManager()->getModel() . "_Logos_" . $type;
    }

    /**
     * @param string|null $type
     * @return LogoModel
     */
    public function getGenericLogo($type = null)
    {
        $type = $this->checkType($type);

        $image = $this->getNewLogo($type);

        return $image;
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function hasLogo($type = null)
    {
        $type = $this->checkType($type);

        $logos = $this->getLogos($type);

        if (is_array($logos) && count($logos) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param null $type
     * @param bool $file
     * @return bool|LogoModel
     */
    public function uploadLogo($type = null, $file = false)
    {
        $type = $this->checkType($type);

        $image = $this->getNewLogo($type);
        $file = $file ? $file : $_FILES['Filedata'];

        $uploadError = Nip_File_System::instance()->getUploadError(
            $file,
            $image->extensions
        );

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
                $error = is_array($image->errors) && count($image->errors) > 0
                    ? implode(', ', $image->errors)
                    : 'Error validating file';
                $this->errors['upload'] = 'Error validate:' . $error;
            }
        }

        return false;
    }

    /**
     * @param $request
     * @return $this|void
     */
    public function removeLogo($request)
    {
        $request = is_array($request) ? $request : ['type' => $request];
        $image = $this->getNewLogo($request['type']);

        return $image->delete(true);
    }

    /**
     * @return string
     */
    public function getLogosPath()
    {
        return '/images/' . $this->getManager()->getTable() . '/' . $this->id . '/';
    }
}
