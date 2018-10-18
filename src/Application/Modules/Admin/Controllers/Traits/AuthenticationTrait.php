<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use Nip\Records\Locator\ModelLocator;

/**
 * Class AuthenticationTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AuthenticationTrait
{
    public function checkAuth()
    {
        if (!($this->getRequest()->getControllerName() == "login" && $this->getRequest()->getActionName() == "index")) {
            $this->_checkUser();
        }
    }

    /**
     * @return \Nip\Records\RecordManager
     */
    protected function getUserManager()
    {
        return ModelLocator::get('administrators');
    }

    protected function getNonAuthRedirectURL()
    {
        return $this->Url()->get('admin.login');
    }
}
