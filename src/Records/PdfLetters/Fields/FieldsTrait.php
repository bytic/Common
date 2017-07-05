<?php

namespace ByTIC\Common\Records\PdfLetters\Fields;

use ByTIC\Common\Records\PdfLetters\Fields\Types\AbstractType;
use ByTIC\Common\Records\PdfLetters\PdfLettersTrait;
use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;
use ByTIC\Common\Records\Traits\HasTypes\RecordsTrait as HasTypeRecordsTrait;

/**
 * Class FieldsTrait
 * @package ByTIC\Common\Records\PdfLetters\Fields
 */
trait FieldsTrait
{
    use AbstractRecordsTrait;
    use HasTypeRecordsTrait;

    /**
     * @var null|array
     */
    protected $mergeFields = null;
    /**
     * @var null
     */
    protected $mergeFieldsType = null;

    /**
     * @return array
     */
    public function getMergeFields()
    {
        if ($this->mergeFields === null) {
            $this->initMergeFields();
        }

        return $this->mergeFields;
    }

    /**
     * @param AbstractType $type
     */
    public function populateTagsFromType($type)
    {
        $typeTags = (array)$type->providesTags();
        foreach ($typeTags as $tag) {
            $this->mergeFields[$type->getCategory()][] = $tag;
            $this->mergeFieldsType[$tag] = $type->getName();
        }
    }

    /**
     * @param $tag
     * @return null|string
     */
    public function getFieldTypeFromMergeTag($tag)
    {
        if ($this->mergeFieldsType === null) {
            $this->initMergeFields();
        }
        if (isset($this->mergeFieldsType[$tag])) {
            return $this->mergeFieldsType[$tag];
        }

        return null;
    }

    /**
     * @param array $params
     */
    public function injectParams(&$params = [])
    {
        /** @noinspection PhpUndefinedClassInspection */
        parent::injectParams($params);
        $params['order'][] = ['Y', 'ASC'];
        $params['order'][] = ['X', 'ASC', false];
    }

    /**
     * @return PdfLettersTrait
     */
    abstract public function getLetterManager();

    protected function initMergeFields()
    {
        /** @var AbstractType[] $types */
        $types = $this->getTypes();
        $this->mergeFields = [];
        foreach ($types as $type) {
            $this->populateTagsFromType($type);
        }
    }
}
