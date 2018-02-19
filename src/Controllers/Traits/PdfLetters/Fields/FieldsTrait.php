<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters\Fields;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Records\PdfLetters\Fields\FieldTrait;
use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use Nip\Records\Record;

/**
 * Class FieldsTrait
 * @package ByTIC\Common\Controllers\Traits\PdfLetters\Fields;
 */
trait FieldsTrait
{
    use AbstractControllerTrait;

    /**
     * @var PdfLetterTrait
     */
    protected $pdfLetter;

    /**
     * @var Record
     */
    protected $parent;

    /**
     * @return PdfLetterTrait
     */
    public function addNewModel()
    {
        /** @var PdfLetterTrait $item */
        $item = $this->getModelManager()->getNew();
        if ($this->pdfLetter) {
            $item->populateFromLetter($this->pdfLetter);
            $this->getView()->Breadcrumbs()->addItem(
                $this->getModelManager()->getLetterManager()->getLabel('add'),
                '#'
            );
            return $item;
        }

        return $this->forward('index', 'error');
    }

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
}

