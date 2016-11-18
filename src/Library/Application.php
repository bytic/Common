<?php

namespace ByTIC\Common\Library;

use Nip\Application as NipApplication;

/**
 * Class Application
 * @package ByTIC\Common\Library
 */
abstract class Application extends NipApplication
{
    public function setupConfig()
    {
        parent::setupConfig();
        app('config')->mergeFile(CONFIG_PATH.'general.ini');
    }

    public function setupAutoLoaderCache()
    {
        $this->getAutoLoader()->setCachePath(CACHE_PATH."autoloader".DS);
    }

    public function setupAutoLoaderPaths()
    {
        $this->getAutoLoader()->addDirectory(APPLICATION_PATH);
        $this->getAutoLoader()->addDirectory(LIBRARY_PATH);

        $this->getAutoLoader()->addNamespace($this->getRootNamespace().'Modules\Admin\\', MODULES_PATH.'admin');
        $this->getAutoLoader()->addNamespace(
            $this->getRootNamespace().'Modules\Frontend\\',
            MODULES_PATH.'default'
        );

        ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.LIBRARY_PATH);
    }
}
