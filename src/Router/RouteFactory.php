<?php

namespace ByTIC\Common\Router;

use Nip\Router\RouteCollection;
use Nip\Router\RouteFactory as NipRouteFactory;

/**
 * Class RouteFactory
 * @package ByTIC\Common\Router
 */
class RouteFactory extends NipRouteFactory
{

    /**
     * @param RouteCollection $collection
     * @param $module
     * @param $prefix
     */
    public static function generateGenericModuleDefaultRoutes($collection, $module, $prefix)
    {
        $classBase = ucfirst($module).'_Route_';
        self::generateIndexRoute($collection, $module, $classBase.'Literal', $prefix);
        self::generateStandardRoute($collection, $module.".default", $classBase.'Standard', $prefix);
    }

    /**
     * @param RouteCollection $collection
     * @param $class
     */
    public static function generateModuleDefaultErrorRoutes($collection, $class)
    {
        self::generateLiteralRoute($collection, "default.error.403", $class, '', '/403',
            ["controller" => "error", "action" => "index"]);

        self::generateLiteralRoute($collection, "default.error.404", $class, '', '/404',
            ["controller" => "error", "action" => "index"]);

        self::generateLiteralRoute($collection, "default.error.500", $class, '', '/500',
            ["controller" => "error", "action" => "index"]);
    }
}
