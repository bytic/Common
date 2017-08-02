<?php

namespace ByTIC\Common\Sections;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class MailServiceProvider
 * @package Nip\Staging
 */
class SectionsServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerSections();
    }

    protected function registerSections()
    {
        $sections = new SectionsManager();
        $this->getContainer()->singleton('sections', $sections);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['sections'];
    }
}
