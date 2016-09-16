<?php

namespace ByTIC\Common\Utility;

use Nip\Request;

class Newrelic
{

    static $licence;
    static $name;

    static function getAppname()
    {
        return self::$name;
    }

    static function init($name, $licence)
    {
        if (self::isLoaded()) {
            self::setAppname($name, $licence);
            newrelic_capture_params();
        }
    }

    static function isLoaded()
    {
        return extension_loaded('newrelic');
    }

    static function setAppname($name, $licence)
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
    static function nameTransactionFromRequest($request)
    {

        if (self::isLoaded()) {
            $name[] = $request->getModuleName();
            $name[] = $request->getControllerName();
            $name[] = $request->getActionName();
            self::nameTransaction(implode('/', $name));
        }
    }

    static function nameTransaction($name)
    {
        if (self::isLoaded()) {
            newrelic_name_transaction($name);
        }
    }

    static function getLicence()
    {
        return self::$licence;
    }
}