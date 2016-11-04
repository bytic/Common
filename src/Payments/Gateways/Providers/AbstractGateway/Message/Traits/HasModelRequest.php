<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits;

/**
 * Class HasModelRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits
 *
 */
trait HasModelRequest
{

    /**
     * @return bool
     */
    protected function validateModel()
    {
        $idModel = $this->getModelIdFromRequest();

        return $this->setModelFromId($idModel);
    }

    /**
     * @return int
     */
    public function getModelIdFromRequest()
    {
        $modelKey = $this->getModelIdRequestKey();

        return $this->getHttpRequest()->query->get($modelKey);
    }

    /**
     * @return string
     */
    public function getModelIdRequestKey()
    {
        $modelIdMethod = 'getPaymentsUrlPK';
        if (method_exists($this->getModelManager(), $modelIdMethod)) {
            return $this->getModelManager()->$modelIdMethod();
        }

        return 'id';
    }


    /**
     * @param $idModel
     * @return bool
     */
    protected function setModelFromId($idModel)
    {
        $this->pushData('id', $idModel);
        $model = $this->findModel($idModel);
        if ($model) {
            $this->pushData('model', $model);

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return \Nip\Records\AbstractModels\Record
     */
    protected function findModel($id)
    {
        $field = $this->getModelIdRequestKey();
        if ($field == 'id') {
            return $this->getModelManager()->findOne($id);
        } else {
            $method = 'findOneBy'.ucfirst($field);

            return $this->getModelManager()->$method($id);
        }
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->getDataItem('model');
    }
}
