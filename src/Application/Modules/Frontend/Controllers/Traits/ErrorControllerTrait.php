<?php

namespace ByTIC\Common\Application\Modules\Frontend\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;

/**
 * Class ErrorControllerTrait
 * @package ByTIC\Common\Application\Modules\Frontend\Controllers\Traits
 */
trait ErrorControllerTrait
{
    use AbstractControllerTrait;

    /**
     * @inheritdoc
     */
    public function beforeAction()
    {
        parent::beforeAction();
        $this->getView()->Breadcrumbs()->addItem("Eroare");
    }

    public function index()
    {
        $this->getView()->set('title', "Error");

        $errorType = $this->getRequest()->get('error_type');
        switch ($errorType) {
            case 404:
                $this->getResponse()->setStatusCode(404);
                break;
        }
    }

    public function access()
    {
        $this->getView()->set('title', "Error");
    }
}
