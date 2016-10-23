<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits\HasView;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;
use ByTIC\Common\Records\Traits\HasStatus\RecordTrait;
use Nip\Records\Record;

/**
 * Class PurchaseResponse
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages
 */
abstract class CompletePurchaseResponse extends AbstractResponse
{
    use HasView;

    protected $button = null;

    protected $redirectUrl = null;

    /**
     * @return string
     */
    public function getIconClass()
    {
        $type = $this->getMessageType();
        switch ($type) {
            case 'success':
                return 'fa fa-check-circle';
            case 'error':
                return 'fa fa-exclamation-triangle';
        }

        return 'fa fa-info-circle';
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        $type = 'info';
        switch ($this->getModel()->getStatus()->getName()) {
            case 'active':
                $type = 'success';
                break;
            case 'canceled':
                $type = 'error';
                break;
            case 'error':
                $type = 'error';
                break;

            case 'default':
            case 'pending':
                break;
        }

        return $type;
    }

    /**
     * @return Record|RecordTrait|IsPurchasableModelTrait
     */
    public function getModel()
    {
        return $this->data['model'];
    }

    /**
     * @return string
     */
    public function getIconColor()
    {
        $type = $this->getMessageType();
        switch ($type) {
            case 'success':
                return '#3c763d';
            case 'error':
                return '#e45a5a';
        }

        return '#5aa5e4';
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getModel()->getConfirmStatusTitle();
    }

    /**
     * @param $label
     * @param $href
     * @return $this
     */
    public function setButton($label, $href)
    {
        $this->button = [
            'label' => $label,
            'href' => $href,
        ];

        return $this;
    }

    /**
     * @return $this
     */
    abstract public function processModel();

    /**
     * @return null|string
     */
    public function getButtonLabel()
    {
        return $this->hasButton() ? $this->button['label'] : null;
    }

    /**
     * @return bool
     */
    public function hasButton()
    {
        return is_array($this->button);
    }

    /**
     * @return null|string
     */
    public function getButtonHref()
    {
        return $this->hasButton() ? $this->button['href'] : null;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return $this->getModel()->getStatus()->getName() === 'active';
    }

    /**
     * @return null
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param null $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @return string
     */
    protected function getViewFile()
    {
        return '/confirm';
    }
}

