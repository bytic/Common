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

    /**
     * @inheritdoc
     */
    public function setupAutoLoaderPaths()
    {
        parent::setupAutoLoaderPaths();

        $this->getAutoLoader()->addDirectory($this->path());

        $this->getAutoLoader()->addNamespace(
            $this->getRootNamespace() . 'Models\\',
            $this->path() . DIRECTORY_SEPARATOR . 'models'
        );

        $this->getAutoLoader()->addNamespace(
            $this->getRootNamespace() . 'Modules\\',
            $this->path() . DIRECTORY_SEPARATOR . 'modules'
        );
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
