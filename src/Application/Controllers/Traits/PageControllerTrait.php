<?php

namespace ByTIC\Common\Application\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\HasForms;
use ByTIC\Navigation\Breadcrumbs\Controllers\HasBreadcrumbsTrait;
use Nip\Config\Config;
use Nip\Html\Head\Entities\Favicon;

/**
 * Class PageControllerTrait
 * @package ByTIC\Common\Application\Controllers\Traits
 */
trait PageControllerTrait
{
    use \Nip\Controllers\Traits\AbstractControllerTrait;
    use HasBreadcrumbsTrait;
    use HasForms;

    /**
     * @inheritdoc
     */
    protected function beforeAction()
    {
        $this->setBreadcrumbs();
    }

    /**
     * @inheritdoc
     */
    protected function afterAction()
    {
        $this->setMeta();
        $this->prepareResponseHeaders();
        $this->afterActionViewVariables();

//        $content = $this->getView()->load(
//            '/layouts/'.$this->getLayout(),
//            [],
//            true
//        );
//        $this->getResponse()->setContent($content);
    }

    /**
     * Set meta information
     *
     * @return void
     */
    protected function setMeta()
    {
//        $tagline = Options::instance()->website_tagline->value;
//        $this->getView()->Meta()->setTitleBase('Galantom'.(!empty($tagline) ? ' - '.$tagline : ''));

        $metaConfig = $this->getConfig()->get('META');
        if ($metaConfig instanceof Config) {
            $this->getView()->Meta()->populateFromConfig($metaConfig);
        }

        $favicon = new Favicon();
        $favicon->setBaseDir(IMAGES_URL . '/favicon');
        $favicon->addAll();
        $this->getView()->set('favicon', $favicon);
    }

    /**
     * Prepare headers
     *
     * @return void
     */
    protected function prepareResponseHeaders()
    {
        $this->payload()->addP3PHeader('CP="CAO PSA OUR"');
    }

    protected function afterActionViewVariables()
    {
        $this->getView()->set('forms', $this->getForms());
        $this->getView()->set('_config', $this->getConfig());
//        $this->getView()->set('_stage', config('env'));

        $this->getView()->set('layout', $this->getLayout());
        $this->getView()->set('_layout', $this->getLayout());

        $this->getView()->set('controller', $this->getName());
        $this->getView()->set('action', $this->getAction());
        $this->getView()->set('_module', $this->getRequest()->getModuleName());

        $this->getView()->set('user', $this->_getUser());
        $this->getView()->set('_user', $this->_getUser());
    }

    /**
     * Get Layout Name
     *
     * @return string
     */
    abstract public function getLayout();
}