<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

/**
 * Class AbstractControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractControllerTrait
{
    use AuthenticationTrait;

    protected function beforeAction()
    {
        $this->checkAuth();

        $this->setBreadcrumbs();

        parent::beforeAction();
    }

    protected function setBreadcrumbs()
    {
        $this->getView()->Breadcrumbs()->addItem("Administrare");
    }

    protected function afterAction()
    {
        $this->getView()->set('user', $this->_getUser());

        parent::afterAction();
    }

    protected function getViewObject()
    {
        return new Admin_View();
    }
}
