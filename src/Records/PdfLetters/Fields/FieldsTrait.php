<?php

namespace ByTIC\Common\Records\PdfLetters\Fields;

use ByTIC\Common\Records\PdfLetters\Fields\Types\AbstractType;
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
     * @return array
     */
    public function getMergeFields()
    {
        if ($this->mergeFields === null) {
            $this->initMergeFields();
        }

        return $this->mergeFields;
    }

    protected function initMergeFields()
    {
        $this->mergeFields = $this->generateMergeFields();
    }

    /**
     * @return array
     */
    protected function generateMergeFields()
    {
        /** @var AbstractType[] $types */
        $types = $this->getTypes();
        $tags = [];
        foreach ($types as $type) {
            $type->populateTags($tags);
        }

        return $tags;
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
}
