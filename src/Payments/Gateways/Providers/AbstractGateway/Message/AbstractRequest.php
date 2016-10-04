<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

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
