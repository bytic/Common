<?php

namespace ByTIC\Common\Controllers\Traits\PdfLetters;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Records;

/**
 * Class PdfLettersTrait
 * @package ByTIC\Common\Controllers\Traits\PdfLetters
 */
trait PdfLettersTrait
{
    use AbstractControllerTrait;

    /**
     * @var string
     */
    protected $letterType;

    /**
     * @var Records
     */
    protected $parentManager;

    /**
     * @var Record
     */
    protected $parent;


    public function upload()
    {
        if ($_FILES['letter']) {
            $parent = $this->getParent();
            if ($parent) {
                $letter = $this->getModelFromRequest();

                if (!$letter) {
                    $letter = $this->newPdfLetterRecordFromItemType($parent, $this->getLetterType());
                }

                $file = $letter->getNewFile();
                if ($file->upload($_FILES['letter'])) {
                    $this->flashRedirectLetter($parent, $this->getModelManager()->getMessage('add'));
                } else {
                    $letter->delete();
                    $this->flashRedirectLetter($parent, implode('. ', $file->getErrors()), 'error');
                }

                die('end');
            }

            die('no valid item');
        }
    }

    public function downloadExample()
    {
        $letter = $this->getModelFromRequest();
        $item = $letter->getItem();

        if ($letter->hasFile()) {
            $letter->downloadExample();
        }

        $this->flashRedirectLetter($item, $this->getModelManager()->getMessage('no-file'), 'error');
    }

    public function downloadBlank()
    {
        $letter = $this->getModelFromRequest();
        $parent = $letter->getItem();
        if ($letter->hasFile()) {
            $letter->downloadBlank();
            die('');
        }

        $this->flashRedirectLetter($parent, $this->getModelManager()->getMessage('no-file'), 'error');
    }

    /**
     * @param Record $parent
     * @param $message
     * @param string $type
     */
    protected function flashRedirectLetter($parent, $message, $type = 'success')
    {
        $this->flashRedirect(
            $message,
            $this->getPdfLettersPageUrl($parent),
            $type,
            $this->getPdfLettersPageController($parent)
        );
    }

    /**
     * @param Record $parent
     * @return mixed
     */
    protected function getPdfLettersPageUrl($parent)
    {
        return $parent->getURL();
    }

    /**
     * @param Record $parent
     * @return mixed
     */
    protected function getPdfLettersPageController($parent)
    {
        return $parent->getManager()->getController();
    }

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

    /**
     * @return Records
     */
    protected function getParentManager()
    {
        return $this->parentManager;
    }

    /**
     * @return Record
     */
    protected function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    protected function getLetterType()
    {
        return $this->letterType;
    }

    /**
     * Called before action
     */
    protected function parseRequestPdfLetter()
    {
        if ($this->getRequest()->get('id_item') && $this->getRequest()->get('type')) {
            $this->checkRequestForParent();
        } else {
            $this->checkRequestForItem();
        }
    }

    protected function checkRequestForParent()
    {
        $this->letterType = $this->getRequest()->get('type');
        $this->parentManager = $this->getModelManager()->getParentManagerFromType($this->getRequest()->get('type'));
        $this->parent = $this->parentManager->findOne($this->getRequest()->get('id_item'));
        if ($this->parent) {
            $this->setModelFromRequest($this->getModelManager()->getByItem($this->letterType, $this->parent->id));
        }
    }

    protected function checkRequestForItem()
    {
        $letter = $this->getModelFromRequest();
        $this->checkRequestSetFromLetter($letter);
    }

    /**
     * @param PdfLetterTrait $letter
     */
    protected function checkRequestSetFromLetter($letter)
    {
        $this->letterType = $letter->type;
        $this->parent = $letter->getItem();
        $this->parentManager = $letter->getItemsManager();
    }

    /**
     * @inheritdoc
     */
    protected function setBreadcrumbs()
    {
        $this->call('setClassBreadcrumbs', $this->parentManager->getController());
        $this->call('setItemBreadcrumbs', $this->parentManager->getController(), false, [$this->parent]);

        $this->setClassBreadcrumbs();
    }
}
