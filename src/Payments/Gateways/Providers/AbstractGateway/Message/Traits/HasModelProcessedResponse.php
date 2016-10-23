<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits;

use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\Record;

/**
 * Class HasView
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits
 *
 */
trait HasModelProcessedResponse
{

    protected $modelResponseStatus = null;

    /**
     * @return $this
     */
    public function processModel()
    {
        if ($this->canProcessModel()) {
            $this->processModelStatus();
            $this->processModelData();
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function canProcessModel()
    {
        return false;
    }

    protected function processModelStatus()
    {
        $model = $this->getModel();
        $modelStatus = $this->getModel()->getStatus()->getName();
        $newModelStatus = $this->getModelResponseStatus();
        if ($newModelStatus && $modelStatus !== $newModelStatus) {
            if ($newModelStatus == 'active') {
                $model->received = $this->getTransactionDate();
                $model->updateStatus($newModelStatus);
            } elseif ($modelStatus == 'active' && $newModelStatus = 'error') {
                // ignore error status after active received
            } else {
                $model->updateStatus($newModelStatus);
            }
        }
    }

    /**
     * @return Record|RecordTrait|IsPurchasableModelTrait
     */
    public function getModel()
    {
        return $this->getDataProperty('model');
    }

    /**
     * @return null|string
     */
    public function getModelResponseStatus()
    {
        if ($this->modelResponseStatus === null) {
            if ($this->isSuccessful()) {
                $status = 'active';
            } elseif ($this->isCancelled()) {
                $status = 'canceled';
            } elseif ($this->isPending()) {
                $status = 'pending';
            } else {
                $status = 'error';
            }
            $this->modelResponseStatus = $status;
        }

        return $this->modelResponseStatus;
    }

    /**
     * @return false|string
     */
    public function getTransactionDate()
    {
        return date('Y-m-d H:i:s');
    }

    public function processModelData()
    {
    }
}
