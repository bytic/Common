<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Application\Models\Users\Traits\AbstractUsersTrait as Users;
use ByTIC\Common\Application\Models\Users\Traits\AbstractUserTrait as User;

/**
 * Trait HasUser
 * @package ByTIC\Common\Controllers\Traits
 */
trait HasUser
{
    protected $user;

    /**
     * @return User
     */
    protected function _getUser()
    {
        if ( ! $this->user) {
            $this->user = $this->initUser();
        }

        return $this->user;
    }

    protected function initUser()
    {
        return Users::instance()->getCurrent();
    }

    protected function _checkUser()
    {
        if ( ! $this->_getUser()->authenticated()) {
            $this->redirect($this->getNonAuthRedirectURL());
        }
    }

    protected function getNonAuthRedirectURL()
    {
        return $this->URL()->base();
    }
}
