<?php

namespace ByTIC\Common\Records\PdfLetters;

use ByTIC\Common\Records\PdfLetters\Fields\FieldTrait;
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
 * @method FieldTrait[] getCustomFields()
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

    public function downloadExample()
    {
        $result = $this->getModelExample();
        $result->demo = true;

        return $this->download($result);
    }

    /**
     * @return Record
     */
    abstract public function getModelExample();

    /**
     * @param $model
     */
    public function download($model)
    {
        $pdf = $this->generatePdfObj($model);
        $item = $this->getItem();

        if ($model->demo === true) {
            $this->pdfDrawGuidelines();
        }

        $pdf->Output($this->getFileNameFromModel($model).'.pdf', 'D');
        die();
    }

    /**
     * @param Record $model
     * @return FPDI
     */
    public function generatePdfObj($model)
    {
        $pdf = $this->generateNewPdfObj();

        $fields = $this->getCustomFields();
        foreach ($fields as $field) {
            $field->addToPdf($pdf, $model);
        }

        return $pdf;
    }

    /**
     * @return FPDI
     */
    public function generateNewPdfObj()
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
     * @param FPDI $pdf
     */
    protected function pdfDrawGuidelines($pdf)
    {
        for ($pos = 5; $pos < 791; $pos = $pos + 5) {
            if (($pos % 100) == 0) {
                $pdf->SetDrawColor(0, 0, 200);
                $pdf->SetLineWidth(.7);
            } elseif (($pos % 50) == 0) {
                $pdf->SetDrawColor(200, 0, 0);
                $pdf->SetLineWidth(.4);
            } else {
                $pdf->SetDrawColor(128, 128, 128);
                $pdf->SetLineWidth(.05);
            }

            $pdf->Line(0, $pos, 611, $pos);
            if ($pos < 611) {
                $pdf->Line($pos, 0, $pos, 791);
            }
        }
    }

    /**
     * @param $model
     * @return string
     */
    protected function getFileNameFromModel($model)
    {
        return 'letter';
    }

    /**
     * @return string
     */
    protected function getDefaultFileName()
    {
        return 'letter.pdf';
    }
}
