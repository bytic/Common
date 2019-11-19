<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway as AbstractGateway;

/**
 * Class Gateway
 * @package ByTIC\Common\Payments\Gateways\Providers\Mobilpay
 */
class Gateway extends AbstractGateway
{

    /**
     * @param $value
     * @return mixed
     */
    public function setSandbox($value)
    {
        return $this->setParameter('sandbox', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setSignature($value)
    {
        return $this->setParameter('signature', $value);
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->getParameter('signature');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setCertificate($value)
    {
        return $this->setParameter('certificate', $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setPrivateKey($value)
    {
        return $this->setParameter('privateKey', $value);
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->getParameter('privateKey');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $this->validateFilePath('certificate');
        $this->validateFilePath('privateKey');

        if ($this->getSandbox() && is_file($this->getCertificate())) {
            return true;
        }

        return false;
    }

    /**
     * @param $type
     * @return bool|string
     * @throws \Exception
     */
    protected function validateFilePath($type)
    {
        $path = $this->getParameter($type);
        if (strpos($path, DIRECTORY_SEPARATOR) === false) {
            $path = $this->getFileDirectoryPath().$path;
        }

        return $this->setParameter($type, $path);
    }

    /**
     * @return string
     */
    protected function getFileDirectoryPath()
    {
        return $this->getPaymentMethod() ? $this->getPaymentMethod()->getFilesDirectory() : null;
    }

    /**
     * @return mixed
     */
    public function getSandbox()
    {
        return $this->getParameter('sandbox');
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->getParameter('certificate');
    }

    /**
     * @inheritdoc
     */
    public function initialize(array $parameters = [])
    {
        parent::initialize($parameters);

        $this->validateFilePath('certificate');
        $this->validateFilePath('privateKey');

        if ($this->getSandbox() == 'yes') {
            $this->setTestMode(true);
        } else {
            $this->setTestMode(false);
        }

        return $this;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'certificate' => 'public.cer',
            'privateKey' => 'private.key',
        ];
    }
}
