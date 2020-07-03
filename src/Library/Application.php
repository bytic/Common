<?php

namespace ByTIC\Common\Library;

use ByTIC\Common\Library\Application\HasAutoloaderTrait;
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
    use HasAutoloaderTrait;

    public function setupConfig()
    {
        parent::setupConfig();
        app('config')->mergeFile(CONFIG_PATH.'general.ini');
    }


    public function setupURLConstants()
    {
        parent::setupURLConstants();
        $this->setupURLConstantsFromFile();
    }

    protected function setupURLConstantsFromFile()
    {
        require_once(ROOT_PATH.'config.php');
    }

    /**
     * @return array
     */
    public function getGenericProviders()
    {
        $config = require dirname(dirname(__DIR__)).'/config/app.php';

        return $config['providers'];
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
