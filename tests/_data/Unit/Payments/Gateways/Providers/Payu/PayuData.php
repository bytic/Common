<?php

namespace ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Payu;

/**
 * Class PayuData
 * @package ByTIC\Common\Tests\Data\Unit\Payments\Gateways\Providers\Payu
 */
class PayuData
{
    /**
     * @return string
     */
    public static function getMethodOptions()
    {
        return trim(file_get_contents(\Codeception\Configuration::dataDir().'\PaymentGateways\PayuOptions.serialized'));
    }
}
