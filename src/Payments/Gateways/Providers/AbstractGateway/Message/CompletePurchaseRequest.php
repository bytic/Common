<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use Nip\Records\RecordManager;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class CompletePurchaseRequest extends AbstractRequest
{

    /**
     * @param  string $value
     * @return mixed
     */
    public function setModelManager($value)
    {
        return $this->setParameter('modelManager', $value);
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return bool
     */
    public function sendData($data)
    {
        return $this->response = false;
    }

    /**
     * @param $id
     * @return \Nip\Records\AbstractModels\Record
     */
    protected function findModel($id)
    {
        return $this->getModelManager()->findOne($id);
    }

    /**
     * @return RecordManager
     */
    public function getModelManager()
    {
        return $this->getParameter('modelManager');
    }

}
