<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters;

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

    abstract protected function getModelFromRequest();
}
