<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use Administrator;
use Administrators;

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
     * @return Administrator
     */
    protected function initUser()
    {
        return Administrators::instance()->getCurrent();
    }

    protected function getNonAuthRedirectURL()
    {
        return $this->Url()->get('admin.login');
    }

    /**
     * @return Administrator
     */
    abstract protected function _getUser();
}
