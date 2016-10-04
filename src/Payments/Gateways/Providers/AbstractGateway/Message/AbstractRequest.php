<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

/**
 * Class AbstractRequest
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

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
}
