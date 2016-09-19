<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

/**
 * Class HasStatus
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method Record|RecordTrait getModelFromRequest
 * @method RecordManager getModelManager
 */
trait HasStatus
{

    public function initViewStatuses()
    {
        $this->getView()->set('statuses', $this->getModelManager()->getStatuses());
    }

    public function changeStatus()
    {
        $item = $this->getModelFromRequest();

        $status = $_GET['status'];
        $redirect = $_SERVER['HTTP_REFERER'];
        $availableStatuses = $this->getModelManager()->getStatusProperty('name');
        if (in_array($status, $availableStatuses)) {
            $item->setStatus($status);
            $this->changeStatusRedirect($item);
        }
        $this->flashRedirect($this->getModelManager()->getMessage('status.invalid-status'), $redirect, 'error');
    }

    /**
     * @param $item
     */
    public function changeStatusRedirect($item)
    {
        $redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $item->getURL();
        $this->flashRedirect($this->getModelManager()->getMessage('status.success'), $redirect);
    }
}
