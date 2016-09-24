<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Request;
use Nip\View;

/**
 * Class HasView
 * @package ByTIC\Common\Controllers\Traits
 */
trait HasView
{

    /**
     * @var View
     */
    protected $_view;

    /**
     * @var string
     */
    protected $layout = 'default';

    /**
     * @return View
     */
    public function loadView()
    {
        echo $this->getView()->load($this->getLayoutPath());
    }

    /**
     * @return View
     */
    public function getView()
    {
        if (!$this->_view) {
            $this->_view = $this->initView();
        }

        return $this->_view;
    }

    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * @return View
     */
    protected function initView()
    {
        $view = $this->getViewObject();
        $view = $this->populateView($view);

        return $view;
    }

    /**
     * @return View
     */
    protected function getViewObject()
    {
        return new \App_View();
    }

    /**
     * @param View $view
     * @return View
     */
    protected function populateView($view)
    {
        $view->setBasePath(MODULES_PATH.$this->getRequest()->getModuleName().'/views/');
        $view = $this->initViewVars($view);
        $view = $this->initViewContentBlocks($view);

        return $view;
    }

    /**
     * @return Request
     */
    abstract public function getRequest();

    /**
     * @param View $view
     * @return View
     */
    protected function initViewVars($view)
    {
        $view->setRequest($this->getRequest());

        $this->controller = $this->getRequest()->getControllerName();
        $view->set('controller', $this->controller);

        $this->action = $this->getRequest()->getActionName();
        $view->set('action', $this->action);

        $view->options = $this->options;

        return $view;
    }

    /**
     * @param View $view
     * @return View
     */
    protected function initViewContentBlocks($view)
    {
        $view->setBlock('content', $this->getRequest()->getControllerName().'/'.$this->getRequest()->getActionName());

        return $view;
    }

    /**
     * @return string
     */
    public function getLayoutPath()
    {
        return '/layouts/'.$this->getLayout();
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
}
