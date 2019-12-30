<?php

namespace ByTIC\Common\Payments\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\CompletePurchaseResponse;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\ServerCompletePurchaseResponse;
use ByTIC\Common\Payments\Models\Purchase\Traits\IsPurchasableModelTrait;

/**
 * Class PurchaseControllerTrait
 * @package ByTIC\Common\Payments\Controllers\Traits
 * @deprecated use \ByTIC\Payments\Controllers\Traits\AdminPaymentMethodsTrait
 *
 * @method IsPurchasableModelTrait checkItem
 */
trait PurchaseControllerTrait
{
    use AbstractControllerTrait;
    use \ByTIC\Payments\Controllers\Traits\AdminPaymentMethodsTrait;
}
