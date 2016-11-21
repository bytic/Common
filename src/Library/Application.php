<?php

namespace ByTIC\Common\Library;

use Nip\Application as NipApplication;
use Nip\Http\Response\Response;
use Nip\Request;

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

        $this->getAutoLoader()->addNamespace($this->getRootNamespace().'Models\\', APPLICATION_PATH.'models');

        $this->getAutoLoader()->addNamespace($this->getRootNamespace().'Modules\Admin\\', MODULES_PATH.'admin');
        $this->getAutoLoader()->addNamespace(
            $this->getRootNamespace().'Modules\Frontend\\',
            MODULES_PATH.'default'
        );

        ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.LIBRARY_PATH);
    }

    public function setupURLConstants()
    {
        parent::setupURLConstants();
        require_once(ROOT_PATH.'config.php');
    }

    public function initLanguages()
    {
        $stageConfig = $this->getStaging()->getStage()->getConfig();
        $availableLanguages = explode(',', $stageConfig->get('LOCALE.languages'));
        $availableLanguages = is_array($availableLanguages) ? $availableLanguages : ['ro'];

        $translator = $this->getTranslator();

        $backend = new \Nip\I18n\Translator\Backend\File();
        $translator->setBackend($backend);

        foreach ($availableLanguages as $language) {
            $backend->addLanguage($language, LANGUAGES_PATH.$language.DS);
        }

        $translator->setDefaultLanguage($stageConfig->get('LOCALE.language_default'));
    }

    public function preHandleRequest()
    {
        parent::preHandleRequest();
        register_shutdown_function('__shutdown');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function filterResponse(Response $response, Request $request)
    {
        parent::filterResponse($response, $request);

        if (!$request->isCLI() && $this->getStaging()->getStage()->inTesting()) {
            $this->getDebugBar()->injectDebugBar($response);
        }

        return $response;
    }
}
