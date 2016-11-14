<?php

namespace ByTIC\Common\Application\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\HasForms;
use Nip\View;

/**
 * Class PageControllerTrait
 * @package ByTIC\Common\Application\Controllers\Traits
 */
trait PageControllerTrait
{
    use HasForms;

    protected function beforeAction()
    {
        parent::beforeAction();
        $this->getView()->set('user', $this->_getUser());
        $this->getView()->set('_user', $this->_getUser());
        $this->setBreadcrumbs();
    }

    /**
     * @return View
     */
    public abstract function getView();

    protected function setBreadcrumbs()
    {
    }

    protected function afterAction()
    {
        header("Content-Type: text/html; charset=utf-8");

        $this->setMeta();
        $this->getView()->set('forms', $this->getForms());
        $this->getView()->_config = $this->getConfig();
        $this->getView()->_stage = app('kernel')->getStaging()->getStage();
        $this->getView()->layout = $this->getLayout();
        $this->getView()->_module = $this->getRequest()->getModuleName();

        $content = $this->getView()->load('/layouts/'.$this->getLayout(), [], true);
        $this->getResponse()->setContent($content);

        parent::afterAction();
    }

    protected function setMeta()
    {
        $tagline = Options::instance()->website_tagline->value;
        $this->getView()->Meta()->setTitleBase('Galantom'.(!empty($tagline) ? ' - '.$tagline : ''));
        $this->getView()->Meta()->authors = explode(",", $this->getConfig()->META->authors);
        $this->getView()->Meta()->description = $this->getConfig()->META->description;
        $this->getView()->Meta()->addKeywords(explode(",", $this->getConfig()->META->keywords));
        $this->getView()->Meta()->copyright = $this->getConfig()->META->copyright;
        $this->getView()->Meta()->robots = $this->getConfig()->META->robots;
        $this->getView()->Meta()->verify_v1 = $this->getConfig()->META->verify_v1;

        $favicon = new \Nip\Html\Head\Entities\Favicon();
        $favicon->setBaseDir(IMAGES_URL.'/favicon');
        $favicon->addAll();
        $this->getView()->set('favicon', $favicon);
    }

    protected function setClassBreadcrumbs()
    {
    }
}
