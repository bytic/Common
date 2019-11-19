<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Payu\Message;

/**
 * Class Helper
 * @package ByTIC\Common\Payments\Gateways\Providers\Payu\Message
 */
class Helper
{

    /**
     * @param array $array
     * @return string
     */
    public static function generateHashFromArray(array $array)
    {
        $return = "";
        for ($i = 0; $i < count($array); $i++) {
            $return .= self::generateHashFromString($array[$i]);
        }

        return $return;
    }

    /**
     * @param $string
     * @return string
     */
    public static function generateHashFromString($string)
    {
        $string = stripslashes($string);
        $size = strlen($string);
        $return = $size . $string;

        return $return;
    }

    /**
     * @param $data
     * @param $secretKey
     * @return string
     */
    public static function generateHmac($data, $secretKey)
    {
        $b = 64; // byte length for md5
        if (strlen($secretKey) > $b) {
            $secretKey = pack("H*", md5($secretKey));
        }
        $key = str_pad($secretKey, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    /**
     * @param $country
     * @return string
     */
    public static function generateCountryCode($country)
    {
        $transformation = [
            'romania' => 'RO',
        ];

        return strtr(strtolower($country), $transformation);
    }
}
