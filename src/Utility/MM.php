<?php

namespace ByTIC\Common\Utility;

use ByTIC\Common\Records\Records;

/**
 * Class MM
 * @package ByTIC\Common\Utility
 */
class MM
{
    /**
     * @param string $name
     *
     * @return Records
     */
    public static function get($name)
    {
        $class = self::getNamespaced($name);
        if (is_object($class)) {
            return $class;
        }
        return self::initClass($name);
    }

    /**
     * Get ModelManager from namespaced class
     *
     * @param string $name
     *
     * @return mixed
     */
    protected static function getNamespaced($name)
    {
        $baseNamespace = app('app')->getRootNamespace() . 'Models\\';
        if (strpos($name, '\\') === false) {
            $name = $name . '\\' . $name;
        }
        $class = $baseNamespace . $name;
        if (class_exists($class)) {
            return self::initClass($class);
        }
        return false;
    }

    /**
     * Return Model Manager Singleton
     *
     * @param string $class
     *
     * @return Records
     */
    public static function initClass($class)
    {
        return call_user_func([$class, 'instance']);
    }
}
