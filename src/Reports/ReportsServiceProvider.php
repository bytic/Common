<?php

namespace ByTIC\Common\Reports;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

/**
 * Class ReportsServiceProvider
 *
 * @package ByTIC\Common\Reports
 */
class ReportsServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerDispatcher();
    }

    protected function registerDispatcher()
    {
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['reports'];
    }
}
