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
     * @return string
     */
    abstract public function getCategory();
}
