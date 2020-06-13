<?php

namespace ByTIC\Common\Application\Modules\Frontend\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\PageControllerTrait as BasePageControllerTrait;
use ByTIC\Common\Controllers\Traits\HasModels;
use ByTIC\Common\Controllers\Traits\HasUser;
use ByTIC\Common\Controllers\Traits\PageTrait;
use Nip\Controllers\Traits\HasViewTrait;
use function Nip\url;

/**
 * Class PageControllerTrait
 *
 * @package ByTIC\Common\Application\Modules\Frontend\Controllers\Traits
 */
trait PageControllerTrait
{
    use BasePageControllerTrait;
    use HasUser;
    use HasModels;
    use PageTrait;
    use HasViewTrait;

    protected function beforeAction()
    {
        $this->getView()->set(
            'messages',
            [
                "error" => flash_get("error"),
                "success" => flash_get("success"),
                "warning" => flash_get("warning"),
            ]
        );

        $this->setBreadcrumbs();
    }

    /**
     * Set Breadcrumbs
     *
     * @return void
     */
    protected function setBreadcrumbs()
    {
        $this->getView()->Breadcrumbs()->addItem("Home", url()->to('/'));
    }
}
