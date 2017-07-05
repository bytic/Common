<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Request;
use Nip\View;

/**
 * Class HasView
 *
 * @package ByTIC\Common\Controllers\Traits
 */
trait HasView
{

    /**
     * @var View
     */
    protected $view;

    /**
     * @var string
     */
    protected $layout = 'default';

    /**
     * @inheritdoc
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
        if (!$this->view) {
            $this->view = $this->initView();
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

    /**
     * Init view Object
     *
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
        $view = $this->initViewVars($view);
        $view = $this->initViewContentBlocks($view);

        return $view;
    }

    /**
     * Init View variables
     *
     * @param View $view View Instance
     *
     * @return View
     */
    protected function initViewVars($view)
    {
        $view->setRequest($this->getRequest());
        $view->set('controller', $this->getName());
        $view->set('action', $this->getRequest()->getActionName());

        return $view;
    }

    /**
     * Returns request instance
     *
     * @return Request
     */
    abstract public function getRequest();

    /**
     * Returns controller name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Init View Content block
     *
     * @param View $view View instance
     *
     * @return View
     */
    protected function initViewContentBlocks($view)
    {
        $view->setBlock(
            'content',
            $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName()
        );

        return $view;
    }

    /**
     * Return layout path
     *
     * @return string
     */
    public function getLayoutPath()
    {
        return '/layouts/' . $this->getLayout();
    }

    /**
     * Return layout name
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout name
     *
     * @param string $layout Layout name
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }


    /**
     * @param self $controller
     * @param Request $newRequest
     * @return static
     */
    protected function prepareCallController($controller, $newRequest)
    {
        $controller = parent::prepareCallController($controller, $newRequest);
        $controller->setView($this->getView());

        return $controller;
    }
}
