<?php

namespace ByTIC\Common\Records\PdfLetters;

use ByTIC\Common\Records\PdfLetters\Fields\FieldTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Records;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait as MediaFileRecordTrait;
use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaGenericRecordTrait;
use Nip_File_System;
use setasign\Fpdi;
use TCPDF;

/**
 * Class PdfLetterTrait
 * @package ByTIC\Common\Records\PdfLetters
 *
 * @method FieldTrait[] getCustomFields()
 *
 * @property int $id_item
 * @property string $type
 * @property string $orientation
 * @property string $format
 */
trait PdfLetterTrait
{
    use MediaGenericRecordTrait;
    use MediaFileRecordTrait;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getManager()->getLabel('title.singular').' #'.$this->id;
    }

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
        $fileName = $this->getFileNameDefault().'.pdf';
        $file->setName($fileName);

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
        /** @noinspection PhpUndefinedFieldInspection */
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

        if ($model->demo === true) {
            $this->pdfDrawGuidelines($pdf);
        }

        $pdf->Output($this->getFileNameFromModel($model).'.pdf', 'D');
        die();
    }

    /**
     * @param Record $model
     * @return FPDI|TCPDF
     */
    public function generatePdfObj($model)
    {
        $pdf = $this->generateNewPdfObj();

        /** @var FieldTrait[] $fields */
        $fields = $this->getCustomFields();
        foreach ($fields as $field) {
            $field->addToPdf($pdf, $model);
        }

        return $pdf;
    }

    /**
     * @return FPDI|TCPDF
     */
    public function generateNewPdfObj()
    {
        /** @var Fpdi|TCPDF $pdf */
        $pdf = new Fpdi\TcpdfFpdi('L');
        $pdf->setPrintHeader(false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(app('config')->get('SITE.name'));

        $pageCount = $pdf->setSourceFile($this->getFile()->getPath());
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplidx = $pdf->importPage($pageNo, '/MediaBox');

            $pdf->addPage(ucfirst($this->orientation), $this->format);
            $pdf->useTemplate($tplidx);
            $pdf->endPage();
        }
        $pdf->setPage(1);

        return $pdf;
    }

    public function downloadBlank()
    {
        $file = $this->getFile()->getPath();

        header('Content-Type: application/pdf');
        header('Content-Description: File Transfer');
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Disposition: attachment; filename="'.basename($file).'";');
        header("Content-Transfer-Encoding: Binary");
        readfile($file);
        die();
    }

    /**
     * @param $model
     * @param $directory
     * @return bool
     */
    public function generateFile($model, $directory)
    {
        $pdf = $this->generatePdfObj($model);

        if ($model->demo === true) {
            $this->pdfDrawGuidelines($pdf);
        }
        $fileName = $this->getFileNameFromModel($model).'.pdf';
        if (is_dir($directory)) {
            return $pdf->Output($directory.$fileName, 'F');
        }

        return false;
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
     * @return string
     */
    protected function getFileNameDefault()
    {
        return 'letter';
    }

    /**
     * @param FPDI|TCPDF $pdf
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

    /** @noinspection PhpUnusedParameterInspection
     * @param $model
     * @return string
     */
    protected function getFileNameFromModel($model)
    {
        return $this->getFileNameDefault();
    }
}
