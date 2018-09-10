<?php

namespace ByTIC\Common\Records\Properties\Types;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as GenericProperty;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Types
 * @deprecated Use \ByTIC\Models\SmartProperties\Properties\Types\Generic
 */
abstract class Generic extends GenericProperty
{

    /**
     * @return string
     */
    protected function getLabelSlug()
    {
        return 'types';
    }
}
