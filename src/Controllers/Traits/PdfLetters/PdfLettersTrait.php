<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters;

use ByTIC\Common\Records\PdfLetters\PdfLettersTrait as PdfLettersRecordTrait;
use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Records;
use Nip\Request;

/**
 * Class PdfLettersTrait
 * @package ByTIC\Common\Controllers\Traits\PdfLetters
 * @deprecated Use \ByTIC\DocumentGenerator\PdfLetters\Controllers\AdminPdfLetterControllerTrait;
 */
trait PdfLettersTrait
{
    Use \ByTIC\DocumentGenerator\PdfLetters\Controllers\AdminPdfLetterControllerTrait;
}
