<?php

namespace ByTIC\Common\Controllers\Traits\Async;

/**
 * Class ResponseTrait
 * @package ByTIC\Common\Controllers\Traits\Async
 */
trait ResponseTrait
{

    /**
     * @var array
     */
    protected $response = [];
    protected $response_type = 'json';

    /**
     * ResponseTrait constructor.
     */
    public function __construct()
    {
        parent::__construct();
        ini_set('html_errors', 0);
    }

    public function afterAction()
    {
        $this->output();
    }

    /**
     * @param string $response
     */
    protected function output($response = '')
    {
        if ($response) {
            $this->response = $response;
        }
        $method = 'output'.strtoupper($this->response_type);
        $this->$method();
        exit();
    }

    /**
     * @param $message
     * @param array $params
     */
    public function sendSuccess($message, $params = [])
    {
        $this->sendResponse('success', $message, $params);
    }

    /**
     * @param $type
     * @param $message
     * @param array $params
     */
    public function sendResponse($type, $message, $params = [])
    {
        $response = $params;
        $response['type'] = $type;
        $response['message'] = $message;

        $this->setResponse($response);
        $this->output();
    }

    /**
     * @param array $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @param $message
     * @param array $params
     */
    public function sendError($message, $params = [])
    {
        $this->sendResponse('error', $message, $params);
    }

    protected function outputJSON()
    {
        header("Content-type: text/x-json");
        echo(json_encode($this->response));
    }

    protected function outputTXT()
    {
        header("Content-type: text/plain");
        echo($this->response);
    }

    protected function outputHTML()
    {
        header("Content-type: text/html");
        echo($this->response);
    }
}
