<?php

namespace ByTIC\Common\Controllers\Traits;

trait HasView
{

    /**
     * @var \App_View
     */
    protected $_view;

    /**
     * @var string
     */
    protected $_layout = 'default';

    /**
     * @return Nip_View
     */
    public function loadView()
    {
        echo $this->getView()->load($this->getLayoutPath());
    }

    /**
     * @return \App_View
     */
    public function getView()
    {
        if (!$this->_view) {
            $this->_view = $this->initView();
        }
        return $this->_view;
    }

    /**
     * @return \App_View
     */
    protected function initView()
    {
        $view = $this->getViewObject();
        $view = $this->populateView($view);
        return $view;
    }


    /**
     * @param \App_View $view
     * @return \App_View
     */
    protected function populateView(\App_View $view)
    {
        $view->setBasePath(MODULES_PATH . $this->getRequest()->getModuleName() . '/views/');
        $view = $this->initViewVars($view);
        $view = $this->initViewContentBlocks($view);
        return $view;
    }


    /**
     * @return \App_View
     */
    protected function initViewVars(\App_View $view)
    {
        $view->controller = $this->controller = $this->getRequest()->controller;
        $view->action = $this->action = $this->getRequest()->getActionName();
        $view->options = $this->options;
        return $view;
    }

    protected function initViewContentBlocks($view)
    {
        $view->setBlock('content', $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName());
        return $view;
    }

    /**
     * @return \App_View
     */
    protected function getViewObject()
    {
        return new \App_View();
    }

    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * @return string
     */
    public function getLayoutPath()
    {
        return '/layouts/' . $this->getLayout();
    }
}