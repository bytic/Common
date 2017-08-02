<?php

namespace ByTIC\Common\Payments\Models\BillingRecord\Traits;

use Nip\Records\Record;

/**
 * Class MethodTrait
 * @package ByTIC\Common\Payments\Models\Methods\Traits
 *
 * @property string $city
 *
 * @method Record getCountry
 */
trait RecordTrait
{
    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();

    /**
     * @return string
     */
    abstract public function getEmail();

    /**
     * @return string
     */
    public function getPurchasePhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getPurchaseCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getPurchaseCountry()
    {
        if ($this instanceof Record) {
            if ($this->hasRelation('Country')) {
                return $this->getCountry()->getName();
            }
        }

        return 'Romania';
    }
}
