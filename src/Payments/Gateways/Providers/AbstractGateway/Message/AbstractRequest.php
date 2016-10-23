<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use Nip\Records\RecordManager;
use Nip\Utility\Traits\NameWorksTrait;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class AbstractRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    use NameWorksTrait;

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
     * Send the request
     *
     * @return ResponseInterface|bool
     */
    public function send()
    {
        if ($this->isProviderRequest()) {
            $data = $this->getData();

            return $this->sendData($data);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isProviderRequest()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->data === null) {
            if ($this->isProviderRequest()) {
                $this->initData();
            }
        }

        return $this->data;
    }

    protected function initData()
    {
        $this->data = false;
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return bool
     */
    public function sendData($data)
    {
        if (is_array($data) && count($data)) {
            $class = $this->getResponseClass();

            return $this->response = new $class($this, $data);
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getResponseClass()
    {
        $class = str_replace('Request', 'Response', $this->getClassFirstName());

        return '\\'.$this->getNamespacePath().'\\'.$class;
    }

    /**
     * @return RecordManager
     */
    public function getModelManager()
    {
        return $this->getParameter('modelManager');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
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
