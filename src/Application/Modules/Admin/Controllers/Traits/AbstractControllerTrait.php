<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait as GenericAbstractControllerTrait;

/**
 * Class AbstractControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractControllerTrait
{
    use AuthenticationTrait;
    use GenericAbstractControllerTrait;

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
        $this->payload()->set('user', $this->_getUser());

        parent::afterAction();
    }

}
