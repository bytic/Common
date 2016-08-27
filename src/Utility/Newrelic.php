<?php

namespace ByTIC\Common\Utility;

use Nip\Request;

class Newrelic
{

    static $licence;


    static function setAppname($name, $licence)
    {
        self::$licence = $licence;
        if (self::isLoaded()) {
            newrelic_set_appname($name, $licence);
        }
    }

    static function init($name, $licence)
    {
        if (self::isLoaded()) {
            self::setAppname($name, $licence);
            newrelic_capture_params();
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

    static function isLoaded()
    {
        return extension_loaded('newrelic');
    }
}