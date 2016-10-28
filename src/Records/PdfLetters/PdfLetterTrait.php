<?php

namespace ByTIC\Common\Records\PdfLetters;

use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Records;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait as MediaFileRecordTrait;
use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaGenericRecordTrait;
use FPDI;
use Nip_File_System;

/**
 * Class PdfLetterTrait
 * @package ByTIC\Common\Records\PdfLetters
 *
 * @property $id_item
 * @property $orientation
 * @property $format
 */
trait PdfLetterTrait
{
    use MediaGenericRecordTrait;
    use MediaFileRecordTrait;

    /**
     * @return Record
     */
    public function getItem()
    {
        $manager = $this->getItemsManager();

        return $manager->findOne($this->id_item);
    }

    /**
     * @return Records
     */
    abstract public function getItemsManager();

    /**
     * @return bool
     */
    public function hasFile()
    {
        $file = $this->getFile();

        return is_file($file->getPath());
    }

    /**
     * @return \ByTIC\Common\Records\Media\Files\Model
     */
    public function getFile()
    {
        $file = $this->getNewFile();
        $file->setName('diploma.pdf');

        return $file;
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $file = $this->getFile();
        $dir = dirname($file->getPath());
        if (is_dir($dir)) {
            Nip_File_System::instance()->removeDirectory($dir);
        }

        /** @noinspection PhpUndefinedClassInspection */
        return parent::delete();
    }

    /**
     * @return FPDI
     */
    public function generatePdfObj()
    {
        $pdf = new FPDI('L');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(app('config')->get('SITE.name'));

        $pagecount = $pdf->setSourceFile($this->getFile()->getPath());
        $tplidx = $pdf->importPage(1, '/MediaBox');

        $pdf->addPage(ucfirst($this->orientation), $this->format);
        $pdf->useTemplate($tplidx);

        return $pdf;
    }

    /**
     * @return string
     */
    protected function getDefaultFileName()
    {
        return 'letter.pdf';
    }
}
