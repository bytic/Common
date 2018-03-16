<?php

namespace ByTIC\Common\Records\Emails\Builder;

use Default_View as DefaultView;
use Nip\View\ViewInterface;

/**
 * Class HasViewTrait
 * @package ByTIC\Common\Records\Emails\Builder
 */
trait HasViewTrait
{
    /**
     * @var null|ViewInterface
     */
    protected $view = null;

    protected $layout = '/layouts/email';

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

    /**
     * @return null|string
     */
    protected function generateEmailBody()
    {
        $this->compileView();
        return $this->getView()->load($this->layout, [], true);
    }

    protected function compileView()
    {
//        $this->getView()->title = $this->getEmail()->subject;
//        $this->getView()->content = $this->getEmail()->body;
        $this->getView()->setBlock('content', '/emails/notifications');
        $this->getView()->set('content', $this->generateEmailContent());
        $this->getView()->set('title', $this->getEmail()->subject);
    }

    /**
     * @return ViewInterface
     */
    public function getView()
    {
        if ($this->view === null) {
            $this->initView();
        }

        return $this->view;
    }

    public function initView()
    {
        $this->view = $this->newView();
    }

    /**
     * @param ViewInterface $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return DefaultView
     */
    public function newView()
    {
        return new DefaultView();
    }

    /**
     * @return null|string
     */
    abstract protected function generateEmailContent();
}
