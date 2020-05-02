<?php

namespace ByTIC\Common\Library\Application;

/**
 * Trait HasAutoloaderTrait
 * @package ByTIC\Common\Library\Application
 *
 * @deprecated Use composer autoloader
 */
trait HasAutoloaderTrait
{

    public function setupAutoLoaderCache()
    {
        $this->getAutoLoader()->setCachePath(CACHE_PATH."autoloader".DS);
    }

    public function setupAutoLoaderPaths()
    {
        $paths = $this->getAutoLoaderClassmapPaths();
        foreach ($paths as $path) {
            $this->getAutoLoader()->addDirectory($path);
        }

        $namespaces = $this->getAutoLoaderNamespaces();
        foreach ($namespaces as $namespace => $path) {
            $this->getAutoLoader()->addNamespace($namespace, $path);
        }

        ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.LIBRARY_PATH);
    }

    /**
     * @return array
     */
    protected function getAutoLoaderClassmapPaths()
    {
        return [APPLICATION_PATH, LIBRARY_PATH];
    }

    /**
     * @return array
     */
    protected function getAutoLoaderNamespaces()
    {
        return [
            $this->getRootNamespace().'Models\\' => APPLICATION_PATH.'models',
            $this->getRootNamespace().'Modules\\' => MODULES_PATH,
            $this->getRootNamespace().'Admin\\' => MODULES_PATH.'admin',
            $this->getRootNamespace().'Frontend\\' => MODULES_PATH.'default',
            $this->getRootNamespace().'Organizers\\' => MODULES_PATH.'organizers',
        ];
    }
}