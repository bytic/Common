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
 */
trait PdfLettersTrait
{


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

    public function downloadExample()
    {
        $letter = $this->getModelFromRequest();
        $item = $letter->getItem();

        if ($letter->hasFile()) {
            $letter->downloadExample();
        }

        $this->flashRedirectLetterError($item, 'no-file');
    }

    public function downloadBlank()
    {
        $letter = $this->getModelFromRequest();
        $item = $letter->getItem();
        if ($letter->hasFile()) {
            $letter->downloadBlank();
            die('');
        }

        $this->flashRedirectLetterError($item, 'no-file');
    }

    /**
     * @return Records
     */
    public function getParentManager()
    {
        return $this->parentManager;
    }

    /**
     * @param Record $item
     * @param $error
     */
    protected function flashRedirectLetterError($item, $error)
    {
        $this->flashRedirect(
            $this->getModelManager()->getMessage($error),
            $item->getURL(),
            'error',
            $item->getManager()->getController()
        );
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
     * Called before action
     */
    protected function parseRequest()
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
    }

    protected function checkRequestForItem()
    {
        $letter = $this->getModelFromRequest();
        $this->letterType = $letter->type;
        $this->parent = $letter->getItem();
        $this->parentManager = $letter->getItemsManager();
    }

    /**
     * @param int $skip
     */
    protected function setBreadcrumbs($skip = 0)
    {
        $this->call('setClassBreadcrumbs', $this->parentManager->getController());
        $this->call('setItemBreadcrumbs', $this->parentManager->getController(), false, [$this->parent]);

        $this->setClassBreadcrumbs();
    }

    /**
     * @return Record|PdfLetterTrait
     */
    abstract protected function getModelFromRequest();

    /**
     * @return Records|PdfLettersRecordTrait
     */
    abstract protected function getModelManager();

    /**
     * @return Request
     */
    abstract protected function getRequest();

    /**
     * @param $message
     * @param $url
     * @param string $type
     * @param bool $name
     * @return mixed
     */
    abstract protected function flashRedirect($message, $url, $type = 'success', $name = false);
}
