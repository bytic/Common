<?php

namespace ByTIC\Common\Controllers\Traits;

use Users;
use User;

trait HasUser
{
    protected $_user;

    /**
     * @return User
     */
    protected function _getUser()
    {
        if (!$this->_user) {
            $this->_user = $this->initUser();
        }
        return $this->_user;
    }

    protected function initUser()
    {
        return Users::instance()->getCurrent();
    }

    protected function _checkUser()
    {
        if (!$this->_getUser()->authenticated()) {
            $this->redirect($this->getNonAuthRedirectURL());
        }
    }

    protected function getNonAuthRedirectURL()
    {
        return $this->URL()->base();
    }
}
