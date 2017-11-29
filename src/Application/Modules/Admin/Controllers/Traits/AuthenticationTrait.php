<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Records\Record;
use function Nip\recordManager;

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
     * @return Record
     * @throws \Nip\AutoLoader\Exception
     */
    protected function initUser()
    {
        return recordManager('Administrators')->getCurrent();
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
