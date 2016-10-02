<?php

namespace ByTIC\Common\Payments\Models\BillingRecord\Traits;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Models\Methods\Traits
 */
trait RecordTrait
{
    /**
     * @return string
     */
    public abstract function getFirstName();

    /**
     * @return string
     */
    public abstract function getLastName();

    /**
     * @return string
     */
    public abstract function getEmail();
}
