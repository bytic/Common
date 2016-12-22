<?php

namespace ByTIC\Common\Application\Models\Currencies\Traits;

use ByTIC\Common\Application\Models\Currencies\Traits\CurrencyRecordTrait as Currency;

/**
 * Class CurrencyRecordsTrait
 * @package ByTIC\Common\Application\Models\Currencies\Traits
 */
trait CurrencyRecordsTrait
{

    /**
     * @param $from
     * @param $to
     * @param $amount
     * @return float|int
     */
    public function convert($from, $to, $amount)
    {
        $rates['eur'] = '1';
        $rates['bgn'] = '0.5113';
        $rates['ron'] = '0.2260';

        return $amount * $rates[$from] / $rates[$to];
    }

    /**
     * @param $code
     * @return Currency
     */
    public function getByCode($code)
    {
        return $this->findOne($code);
    }

    /**
     * @param $id
     * @return Currency
     */
    abstract function findOne($id);
}