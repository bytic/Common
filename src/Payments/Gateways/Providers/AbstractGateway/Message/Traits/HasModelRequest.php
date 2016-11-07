<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits;

use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Record;

/**
 * Class HasModelRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits
 *
 */
trait HasModelRequest
{

    /**
     * Returns ID if it has it
     * @return int
     */
    public function getModelIdFromRequest()
    {
        return false;
    }

    /**
     * returns key in confirm URL Query
     * @return int
     */
    public function getModelPKFromRequestQuery()
    {
        $modelKey = $this->getModelUrlPkRequestKey();

        return $this->getHttpRequest()->query->get($modelKey);
    }

    /**
     * @return string
     */
    public function getModelUrlPkRequestKey()
    {
        $modelIdMethod = 'getPaymentsUrlPK';
        if (method_exists($this->getModelManager(), $modelIdMethod)) {
            return $this->getModelManager()->$modelIdMethod();
        }

        return 'id';
    }

    /**
     * @return bool
     */
    protected function validateModel()
    {
        $model = $this->generateModelFromRequest();
        if ($this->isValidModel($model)) {
            $this->setModel($model);
            return true;
        }

        return false;
    }

    /**
     * @return bool|IsPurchasableModelTrait|Record
     */
    protected function generateModelFromRequest()
    {
        $model = $this->generateModelFromRequestBody();
        if ($this->isValidModel($model)) {
            return $model;
        } else {
            return $this->generateModelFromRequestQuery();
        }
    }

    /**
     * @param $model
     * @return bool
     */
    protected function isValidModel($model)
    {
        return is_object($model);
    }

    /**
     * @return bool|IsPurchasableModelTrait
     */
    protected function generateModelFromRequestQuery()
    {
        $pkValue = $this->getModelPKFromRequestQuery();
        $field = $this->getModelUrlPkRequestKey();
        if ($pkValue) {
            return $this->findModelByField($field, $pkValue);
        }
        return false;
    }

    /**
     * @return bool|IsPurchasableModelTrait|Record
     */
    protected function generateModelFromRequestBody()
    {
        $idModel = $this->getModelIdFromRequest();
        if ($idModel > 0) {
            return $this->findModel($idModel);
        }
        return false;
    }

    /**
     * @param $idModel
     * @return bool
     */
    protected function setModelFromId($idModel)
    {
        $model = $this->findModel($idModel);
        if ($model) {
            $this->setModel($model);
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return IsPurchasableModelTrait|Record
     */
    protected function findModel($id)
    {
        return $this->getModelManager()->findOne($id);
    }

    /**
     * @param $field
     * @param $value
     * @return IsPurchasableModelTrait
     */
    protected function findModelByField($field, $value)
    {
        if ($field == 'id') {
            return $this->findModel($value);
        }
        $method = 'findOneBy' . ucfirst($field);
        return $this->getModelManager()->$method($value);
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->getDataItem('model');
    }

    /**
     * @param $model
     * @return $this
     */
    protected function setModel($model)
    {
        $this->pushData('id', $model->id);
        $this->pushData('model', $model);
        return $this;
    }
}
