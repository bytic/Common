<?php

namespace ByTIC\Common\Records\Traits\HasForms;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;
use Nip\FrontController;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\HasForms
 */
trait RecordsTrait
{

    use AbstractRecordsTrait;

    protected $formClassNameSlug = null;

    /**
     * @param null $type
     * @return mixed
     */
    public function newForm($type = null)
    {
        $class = $this->getFormClassName($type);

        return new $class;
    }

    /**
     * @param $type
     * @return string
     */
    public function getFormClassName($type)
    {
        if (!$type) {
            $type = $this->getFormTypeDefault();
        }

        $module = FrontController::instance()->getRequest()->getModuleName();
        if (strpos($type, 'admin-') !== false) {
            $module = 'admin';
            $type = str_replace('admin-', '', $type);
        } elseif (strpos($type, 'default-') !== false) {
            $type = str_replace('default-', '', $type);
        }

        $name = ucfirst($module).'_Forms_';
        $name .= $this->getFormClassNameSlug().'_';
        $name .= inflector()->classify($type);

        return $name;
    }

    /**
     * @return string
     */
    public function getFormTypeDefault()
    {
        return 'Details';
    }

    /**
     * @return mixed|null
     */
    public function getFormClassNameSlug()
    {
        if ($this->formClassNameSlug == null) {
            $this->initFormClassNameSlug();
        }

        return $this->formClassNameSlug;
    }

    /**
     * @param null $formClassNameSlug
     */
    public function setFormClassNameSlug($formClassNameSlug)
    {
        $this->formClassNameSlug = $formClassNameSlug;
    }

    protected function initFormClassNameSlug()
    {
        $slug = \inflector()->singularize(\inflector()->classify($this->getFormClassNameBase()));
        $this->setFormClassNameSlug($slug);
    }

    /**
     * @return string
     */
    public function getFormClassNameBase()
    {
        return $this->getTable();
    }
}
