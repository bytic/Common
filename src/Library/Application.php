<?php

namespace ByTIC\Common\Library;

use Nip\Application\Application as NipApplication;
use Nip\Config\Config;
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
        app('config')->mergeFile(CONFIG_PATH . 'general.ini');

        $config = new Config([
            'filesystems' => [
                'disks' => [
                    'local' => [
                        'driver' => 'local',
                        'root' => UPLOADS_PATH,
                    ],
                    'public' => [
                        'driver' => 'local',
                        'root' => UPLOADS_PATH,
                        'url' => UPLOADS_URL,
                        'visibility' => 'public',
                    ]
                ]
            ]
        ]);
        app('config')->merge($config);
    }

    public function setupAutoLoaderCache()
    {
        $this->getAutoLoader()->setCachePath(CACHE_PATH . "autoloader" . DS);
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

        ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . LIBRARY_PATH);
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
            $this->getRootNamespace() . 'Models\\' => APPLICATION_PATH . 'models',
            $this->getRootNamespace() . 'Modules\\' => MODULES_PATH,
            $this->getRootNamespace() . 'Admin\\' => MODULES_PATH . 'admin',
            $this->getRootNamespace() . 'Frontend\\' => MODULES_PATH . 'default',
            $this->getRootNamespace() . 'Organizers\\' => MODULES_PATH . 'organizers',
        ];
    }

    public function setupURLConstants()
    {
        parent::setupURLConstants();
        $this->setupURLConstantsFromFile();
    }

    protected function setupURLConstantsFromFile()
    {
        require_once(ROOT_PATH . 'config.php');
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
            $backend->addLanguage($language, LANGUAGES_PATH . $language . DS);
        }

        $translator->setDefaultLanguage($stageConfig->get('LOCALE.language_default'));
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
