<?php

namespace ByTIC\Common\Utility;

use Nip\Request;

/**
 * Class Newrelic
 * @package ByTIC\Common\Utility
 */
class Newrelic
{
    public static $licence;
    public static $name;

    /**
     * Get the App Name
     *
     * @return string
     */
    public static function getAppname()
    {
        return self::$name;
    }

    /**
     * @param $name
     * @param $licence
     */
    public static function init($name, $licence)
    {
        if (self::isLoaded()) {
            self::setAppname($name, $licence);
            newrelic_capture_params();
        }
    }

    /**
     * @return bool
     */
    public static function isLoaded()
    {
        return extension_loaded('newrelic');
    }

    /**
     * Set the App Name
     *
     * @param $name
     * @param $licence
     */
    public static function setAppname($name, $licence)
    {
        self::$licence = $licence;
        self::$name = $name;
        if (self::isLoaded()) {
            newrelic_set_appname($name, $licence);
        }
    }

    /**
     * @param Request $request
     */
    public static function nameTransactionFromRequest($request)
    {
        if (self::isLoaded()) {
            $name[] = $request->getModuleName();
            $name[] = $request->getControllerName();
            $name[] = $request->getActionName();
            self::nameTransaction(implode('/', $name));
        }
    }

    public static function nameTransaction($name)
    {
        if (self::isLoaded()) {
            newrelic_name_transaction($name);
        }
    }

    /**
     * Get Licence key
     *
     * @return string
     */
    public static function getLicence()
    {
        return self::$licence;
    }
}
