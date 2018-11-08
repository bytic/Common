<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use Administrator;
use Administrators;
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
     * @return \Nip\Records\RecordManager|Users
     */
    protected function getUserManager()
    {
        return ModelLocator::get('administrators');
    }

    /**
     * @return mixed
     */
    protected function getNonAuthRedirectURL()
    {
        return $this->Url()->get('admin.login');
    }

    /**
     * @return Administrator
     */
    abstract protected function _getUser();
}
