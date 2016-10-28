<?php

namespace ByTIC\Common\Records\PdfLetters\Fields;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;


/**
 * Class FieldsTrait
 * @package ByTIC\Common\Records\PdfLetters\Fields
 */
trait FieldsTrait
{
    use AbstractRecordsTrait;

    abstract public function getMergeFields();

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
