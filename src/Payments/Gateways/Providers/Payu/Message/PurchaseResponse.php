<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\PurchaseResponse as AbstractPurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\RedirectResponse\RedirectResponseTrait;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * PayU Purchase Response
 */
class PurchaseResponse extends AbstractPurchaseResponse implements RedirectResponseInterface
{
    use RedirectResponseTrait;
}
