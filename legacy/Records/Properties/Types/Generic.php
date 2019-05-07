<?php

namespace ByTIC\Common\Records\Properties\Types;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as GenericProperty;
use ReflectionClass;

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

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function generateNameFromClass()
    {
        if ($this->hasManager()) {
            $namespaceTypes = $this->getManager()->getTypeNamespace();
            $name = (new ReflectionClass($this))->getName();

            return str_replace($namespaceTypes, '', $name);
        }

        return parent::generateNameFromClass();
    }
}
