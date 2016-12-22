<?php

namespace ByTIC\Common\Application\Models\Currencies\Traits;

use ByTIC\Common\Application\Models\Currencies\Traits\CurrencyRecordsTrait as Currencies;
use ByTIC\Common\Application\Models\Currencies\Traits\CurrencyRecordTrait as Currency;

/**
 * Class HasCurrencyRecord
 * @package ByTIC\Common\Application\Models\Currencies\Traits
 */
trait HasCurrencyRecord
{

    protected $currencyObject;

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        if ($this->currencyObject === null) {
            $this->initCurrency();
        }
        return $this->currencyObject;
    }

    public function initCurrency()
    {
        $this->currencyObject = $this->getCurrenciesManager()->getByCode($this->getCurrencyCode());
    }

    /**
     * @return Currencies
     */
    public function getCurrenciesManager()
    {
        return \Currencies::instance();
    }

    /**
     * @return string
     */
    abstract public function getCurrencyCode();
}