<?php

namespace ByTIC\Common\Records\PdfLetters\Fields\Types;

use ByTIC\Common\Records\Properties\Types\Generic;

/**
 * Class AbstractType
 * @package ByTIC\Common\Records\PdfLetters\Fields\Types
 */
abstract class AbstractType extends Generic
{

    /**
     * @param $model
     * @return null
     */
    public function getValue($model)
    {
        return null;
    }

    /**
     * @return string
     */
    public function providesTags()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    abstract public function getCategory();
}
