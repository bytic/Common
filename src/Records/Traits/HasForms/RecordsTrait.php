<?php

namespace ByTIC\Common\Records\Traits\HasForms;

use Nip\FrontController;

trait RecordsTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

    protected $_formClassNameSlug = null;

    public function newForm($type = null)
    {
        $class = $this->getFormClassName($type);
        return new $class;
    }

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

        $name = ucfirst($module) . '_Forms_';
        $name .= $this->getFormClassNameSlug() . '_';
        $name .= inflector()->classify($type);
        return $name;
    }

    public function getFormClassNameSlug()
    {
        if ($this->_formClassNameSlug == null) {
            $this->_formClassNameSlug = \inflector()->singularize(\inflector()->classify($this->getFormClassNameBase()));
        }
        return $this->_formClassNameSlug;
    }

    public function getFormClassNameBase()
    {
        return $this->getTable();
    }

    public function getFormTypeDefault()
    {
        return 'Details';
    }
}