<?php

namespace ByTIC\Common\Application\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\HasForms;
use Nip\Config\Config;
use Nip\Html\Head\Entities\Favicon;
use Nip\Http\Response\Response;
use Nip\View;

/**
 * Class PageControllerTrait
 * @package ByTIC\Common\Application\Controllers\Traits
 */
trait PageControllerTrait
{
    use HasForms;

    /**
     * @return Response
     */
    public abstract function getResponse();

    /**
     * @return View
     */
    public abstract function getView();

    /**
     * @return Config
     */
    public abstract function getConfig();

    protected function beforeAction()
    {
        parent::beforeAction();
        $this->getView()->set('user', $this->_getUser());
        $this->getView()->set('_user', $this->_getUser());
        $this->setBreadcrumbs();
    }

    protected function setBreadcrumbs()
    {
    }

    protected function afterAction()
    {
        $this->setMeta();
        $this->getView()->set('forms', $this->getForms());
        $this->getView()->set('_config', $this->getConfig());
        $this->getView()->set('_stage', app('kernel')->getStaging()->getStage());
        $this->getView()->set('layout', $this->getLayout());
        $this->getView()->set('_module', $this->getRequest()->getModuleName());

        $content = $this->getView()->load('/layouts/'.$this->getLayout(), [], true);
        $this->getResponse()->setContent($content);

        $this->getResponse()->headers->set('Content-Type', 'text/html');
        $this->getResponse()->setCharset('utf-8');

        parent::afterAction();
    }

    protected function setMeta()
    {
//        $tagline = Options::instance()->website_tagline->value;
//        $this->getView()->Meta()->setTitleBase('Galantom'.(!empty($tagline) ? ' - '.$tagline : ''));

        $this->getView()->Meta()->populateFromConfig($this->getConfig()->get('META'));

        $favicon = new Favicon();
        $favicon->setBaseDir(IMAGES_URL.'/favicon');
        $favicon->addAll();
        $this->getView()->set('favicon', $favicon);
    }

    protected function setClassBreadcrumbs()
    {
    }
}
