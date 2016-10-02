<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse;

/**
 * Class RedirectResponseTrait
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Messages\RedirectResponse
 */
trait RedirectResponseTrait
{

    public function isSuccessful()
    {
        return false;
    }

    /**
     * Returns whether the transaction should continue
     * on a redirected page
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * Returns the redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    /**
     * Returns redirect URL method
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Returns the FORM data for the redirect
     *
     * @return array
     */
    public function getRedirectData()
    {
        return $this->getData();
    }
}
