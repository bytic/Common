<?php

namespace ByTIC\Common\Controllers\Traits;

/**
 * Class HasStatus
 * @package ByTIC\Common\Controllers\Traits
 *
 * @property \Nip\Controller $this
 *
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
            $this->item->setStatus($status);
            $this->changeStatusRedirect($item);
        }
        $this->flashRedirect($this->getModelManager()->getMessage('status.invalid-status'), $redirect, 'error');
    }

    public function changeStatusRedirect($item)
    {
        $redirect = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $item->getURL();
        $this->flashRedirect($this->getModelManager()->getMessage('status.success'), $redirect);
    }

}