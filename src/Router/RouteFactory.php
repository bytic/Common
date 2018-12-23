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
     * @param string $module
     * @param string $prefix
     */
    public static function generateGenericModuleDefaultRoutes($collection, $module, $prefix)
    {
        $moduleName = is_array($module) ? $module[0] : $module;
        self::generateIndexRoute(
            $collection,
            $moduleName, self::generateModuleRouteClassBase($module, 'Literal'),
            $prefix
        );
        self::generateStandardRoute(
            $collection,
            $moduleName . ".default",
            self::generateModuleRouteClassBase($module, 'Standard'),
            $prefix
        );
    }

    /**
     * @param string|array $module
     * @param string $type
     * @return string
     */
    public static function generateModuleRouteClassBase($module, $type)
    {
        if (is_array($module)) {
            $namespace = $module[1];
            $module = $module[0];
            $module = $module == 'default' ? 'frontend' : $module;
            return $namespace . 'Modules\\' . ucfirst($module) . '\Routes\\' . ucfirst($type) . 'Route';
        }
        return ucfirst($module) . '_Route_' . ucfirst($type);
    }

    /**
     * @param RouteCollection $collection
     * @param $class
     */
    public static function generateModuleDefaultErrorRoutes($collection, $class)
    {
        self::generateModuleErrorRoutes($collection, $class, 'default');
    }

    /**
     * @param RouteCollection $collection
     * @param $class
     * @param string $module
     */
    public static function generateModuleErrorRoutes($collection, $class, $module = 'default')
    {
        foreach (['403', '404', '500'] as $code) {
            self::generateLiteralRoute(
                $collection,
                $module . ".error." . $code, $class, '', '/' . $code,
                ["controller" => "error", "action" => "index", "error_type" => $code]
            );
        }
    }
}
