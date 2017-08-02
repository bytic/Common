<?php

namespace ByTIC\Common\Records\Traits\HasForms;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;

/**
 * Class RecordsTrait
 *
 * @package ByTIC\Common\Records\Traits\HasForms
 */
trait RecordsTrait
{
    use AbstractRecordsTrait;

    protected $formClassNameSlug = null;

    /**
     * Generate a new form
     *
     * @param null|string $type Type form name
     *
     * @return mixed
     */
    public function newForm($type = null)
    {
        $class = $this->getFormClassName($type);

        return new $class;
    }

    /**
     * Get Form Class name by type
     *
     * @param string $type Type name
     *
     * @return string
     */
    public function getFormClassName($type = null)
    {
        if (!$type) {
            $type = $this->getFormTypeDefault();
        }

        $module = $this->getRequest()->getModuleName();
        if (strpos($type, 'admin-') !== false) {
            $module = 'admin';
            $type = str_replace('admin-', '', $type);
        } elseif (strpos($type, 'default-') !== false) {
            $type = str_replace('default-', '', $type);
        }

        $name = ucfirst($module) . '_Forms_';
        $name .= $this->getFormClassNameSlug() . '_';
        $name .= inflector()->classify($type);

        return $name;
    }

    /**
     * Get type default name
     *
     * @return string
     */
    public function getFormTypeDefault()
    {
        return 'Details';
    }

    /**
     * Get form slug for class name
     *
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
     * Set class name slug for form
     *
     * @param string $formClassNameSlug Class name slug
     *
     * @return void
     */
    public function setFormClassNameSlug($formClassNameSlug)
    {
        $this->formClassNameSlug = $formClassNameSlug;
    }

    /**
     * Init form class name slug
     *
     * @return void
     */
    protected function initFormClassNameSlug()
    {
        $slug = \inflector()->singularize(
            \inflector()->classify($this->getFormClassNameBase())
        );
        $this->setFormClassNameSlug($slug);
    }

    /**
     * Get base for class name
     *
     * @return string
     */
    public function getFormClassNameBase()
    {
        return $this->getController();
    }
}
