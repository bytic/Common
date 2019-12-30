<?php

namespace ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits;

use Nip\View;

/**
 * Class HasView
 * @package ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Message\Traits
 */
trait HasView
{

    /**
     * @var View
     */
    protected $view = null;

    /**
     * @return $this
     */
    public function send()
    {
        echo $this->getViewContent();

        return $this;
    }

    /**
     * @return string
     */
    public function getViewContent()
    {
        $this->getView()->set('response', $this);
        $this->initViewVars();

        return $this->getView()->load($this->getViewFile());
    }

    /**
     * @return View|null
     */
    public function getView()
    {
        if ($this->view === null) {
            $this->initView();
        }

        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    protected function initView()
    {
        $view = $this->newView();
        $view->setBasePath(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Views'.DIRECTORY_SEPARATOR);
        $this->setView($view);
    }

    /**
     * @return View
     */
    protected function newView()
    {
        return new View();
    }

    protected function initViewVars()
    {
    }

    /**
     * @return string
     */
    abstract protected function getViewFile();
}
