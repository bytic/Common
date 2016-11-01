<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters;

use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Records;

/**
 * Class PdfLettersTrait
 * @package ByTIC\Common\Controllers\Traits\PdfLetters
 */
trait PdfLettersTrait
{
    /**
     * @return mixed
     */
    protected function viewCheckItem()
    {
        $letter = $this->getModelFromRequest();

        if (!$letter) {
            $this->redirect($this->getModelManager()->getUploadURL($_GET));
        }
        return $letter;
    }

    /**
     * @return Record
     */
    abstract protected function getModelFromRequest();

    /**
     * @return Records
     */
    abstract protected function getModelManager();

    /**
     * @param $item
     * @param $type
     * @return PdfLetterTrait|Record
     */
    protected function newPdfLetterRecordFromItemType($item, $type)
    {
        $letter = $this->newPdfLetterRecord();
        $letter->id_item = $item->id;
        $letter->type = $type;
        $letter->insert();
        return $letter;
    }

    /**
     * @return Record|PdfLetterTrait
     */
    protected function newPdfLetterRecord()
    {
        return $this->getModelManager()->getNew();
    }
}
