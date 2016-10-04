<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use Nip\Records\RecordManager;

/**
 * Class AbstractRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    protected $data = null;

    protected $liveEndpoint = null;
    protected $testEndpoint = null;

    /**
     * @param  string $value
     * @return mixed
     */
    public function setModelManager($value)
    {
        return $this->setParameter('modelManager', $value);
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @return bool
     */
    public function hasGet()
    {
        foreach (func_get_args() as $key) {
            if (!$this->httpRequest->query->has($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasPOST()
    {
        foreach (func_get_args() as $key) {
            if (!$this->httpRequest->request->has($key)) {
                return false;
            }
        }

        return true;
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
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->initData();
        }
        return $this->data;
    }

    protected function initData()
    {
        $this->data = false;
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

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    protected function pushData($key, $value)
    {
        $this->data = is_array($this->data) ? $this->data : [];
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getDataItem($key)
    {
        return $this->data[$key];
    }
}
