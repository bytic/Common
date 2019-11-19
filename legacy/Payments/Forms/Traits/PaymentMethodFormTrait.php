<?php

namespace ByTIC\Common\Payments\Forms\Traits;

use ByTIC\Common\Forms\Traits\AbstractFormTrait;
use ByTIC\Common\Payments\Gateways\Manager as GatewaysManager;
use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Gateway;
use ByTIC\Common\Payments\Models\Methods\Traits\RecordTrait as PaymentMethod;
use Nip_Form_Element_Abstract as FormElementAbstract;
use Nip_Form_Element_Select as FormSelect;

/**
 * Class PaymentMethodFormTrait
 * @package ByTIC\Common\Payments\Forms\Traits
 * @deprecated use \ByTIC\Payments\Forms\Traits\PaymentMethodFormTrait
 */
trait PaymentMethodFormTrait
{
    use AbstractFormTrait;
    use \ByTIC\Payments\Forms\Traits\PaymentMethodFormTrait;
}
