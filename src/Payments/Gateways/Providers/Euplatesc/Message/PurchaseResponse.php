<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Euplatesc\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseResponse as AbstractPurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse\RedirectTrait;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayU Purchase Response
 */
class PurchaseResponse extends AbstractPurchaseResponse implements RedirectResponseInterface
{
    use RedirectTrait;

    /**
     * @return array
     */
    public function getRedirectData()
    {
        $data = array_merge($this->getDataProperty('order'), $this->getDataProperty('bill'));
        $data['fp_hash'] = $this->getDataProperty('fp_hash');

        return $data;
    }
}
