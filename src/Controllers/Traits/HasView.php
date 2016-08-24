<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\View;

trait HasView
{

    /**
     * @var View
     */
    protected $_view;

    /**
     * @var string
     */
    protected $_layout = 'default';

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
     * @param View $view
     * @return View
     */
    protected function populateView($view)
    {
        $view->setBasePath(MODULES_PATH . $this->getRequest()->getModuleName() . '/views/');
        $view = $this->initViewVars($view);
        $view = $this->initViewContentBlocks($view);
        return $view;
    }


    /**
     * @param View $view
     * @return View
     */
    protected function initViewVars($view)
    {
        $view->setRequest($this->getRequest());
        $view->controller = $this->controller = $this->getRequest()->getControllerName();
        $view->action = $this->action = $this->getRequest()->getActionName();
        $view->options = $this->options;
        return $view;
    }

    /**
     * @param View $view
     * @return View
     */
    protected function initViewContentBlocks($view)
    {
        $view->setBlock('content', $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName());
        return $view;
    }

    /**
     * @return View
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