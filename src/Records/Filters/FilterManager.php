<?php

namespace ByTIC\Common\Records\Filters;

use Nip\Records\Filters\FilterManager as NipFilterManager;

/**
 * Class FilterManager
 *
 * @package ByTIC\Common\Records\Filters
 */
class FilterManager extends NipFilterManager
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->addFilter(
            $this->newFilter('Column\WildcardFilter')->setField('name')
        );

        $this->addFilter(
            $this->newFilter('Column\BasicFilter')->setField('title')
        );
    }
}
