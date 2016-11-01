<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters\Fields;

use ByTIC\Common\Records\PdfLetters\Fields\FieldsTrait as FieldsRecordsTrait;
use ByTIC\Common\Records\PdfLetters\Fields\FieldTrait;
use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use ByTIC\Common\Records\Records;
use Nip\Records\Record;
use Nip\Request;

/**
 * Class FieldsTrait
 * @package ByTIC\Common\Controllers\Traits\PdfLetters\Fields;
 */
trait FieldsTrait
{

    /**
     * @var PdfLetterTrait
     */
    protected $pdfLetter;

    /**
     * @var Record
     */
    protected $parent;

    /**
     * Called before action
     */
    protected function parseRequestPdfLetterField()
    {
        if ($this->getRequest()->get('id_letter')) {
            $this->pdfLetter = $this->checkForeignModelFromRequest(
                $this->getModelManager()->getLetterManager()->getTable(),
                'id_letter'
            );
        } else {
            $field = $this->getModelFromRequest();
            $this->pdfLetter = $field->getPdfLetter();
        }

        $this->parent = $this->pdfLetter->getItem();
    }

    /**
     * @return Records|FieldsRecordsTrait
     */
    abstract protected function getModelManager();

    /**
     * @param bool $key
     * @return FieldTrait|Record
     */
    abstract protected function getModelFromRequest($key = false);

    /**
     * @param $name
     * @param bool $key
     * @return mixed
     */
    abstract protected function checkForeignModelFromRequest($name, $key = false);

    /**
     * @return Request
     */
    abstract protected function getRequest();
}

