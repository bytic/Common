<?php

namespace ByTIC\Common\Application\Modules\Frontend\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;

/**
 * Class PageControllerTrait
 *
 * @package ByTIC\Common\Application\Modules\Frontend\Controllers\Traits
 */
trait PageControllerTrait
{
    use AbstractControllerTrait;

    protected function beforeAction()
    {
        parent::beforeAction();

        $this->getView()->set(
            'messages',
            [
                "error" => flash_get("error"),
                "success" => flash_get("success"),
                "warning" => flash_get("warning")
            ]
        );

        $this->_setBreadcrumbs();

        //Console::disable();
    }

    protected function _setBreadcrumbs()
    {
        $this->getView()->Breadcrumbs()->addItem("Home", BASE_URL);
    }

    protected function _checkUser()
    {
        if (!$this->_getUser()->authenticated()) {
            $this->loginRedirect();
        }
        return $this;
    }
}