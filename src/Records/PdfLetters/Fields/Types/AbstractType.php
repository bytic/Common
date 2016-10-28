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
     * @param $tags
     */
    public function populateTags(&$tags)
    {
        $typeTags = (array)$this->providesTags();
        $categoryTags = isset($tags[$this->getCategory()]) ? $tags[$this->getCategory()] : [];
        $categoryTags = array_merge($categoryTags, $typeTags);
        $tags[$this->getCategory()] = $categoryTags;
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
