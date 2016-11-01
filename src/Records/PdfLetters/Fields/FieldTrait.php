<?php

namespace ByTIC\Common\Records\PdfLetters\Fields;

use ByTIC\Common\Records\PdfLetters\Fields\Types\AbstractType;
use ByTIC\Common\Records\PdfLetters\PdfLetterTrait;
use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Traits\HasTypes\RecordTrait as HasTypeRecordTrait;
use FPDI;

/**
 * Class FieldTrait
 * @package ByTIC\Common\Records\PdfLetters\Fields
 *
 * @property string $field
 * @property string $size
 * @property string $color
 * @property string $align
 * @property string $x
 * @property string $y
 *
 * @method FieldsTrait getManager()
 * @method AbstractType getType()
 */
trait FieldTrait
{
    use HasTypeRecordTrait;

    /**
     * @return string
     */
    public function getName()
    {
        return translator()->translate($this->field);
    }

    /**
     * @return mixed
     */
    public function getTypeValue()
    {
        return $this->getManager()->getFieldTypeFromMergeTag($this->field);
    }

    /**
     * @param FPDI $pdf
     * @param Record $model
     */
    public function addToPdf($pdf, $model)
    {
        $this->pdfPrepareFont($pdf);
        $this->pdfPrepareColor($pdf);

        $value = $this->getValue($model);
        $x = $this->pdfXPosition($pdf, $value);
        $pdf->Text($x, $this->y, $value);
    }

    /**
     * @param Record $model
     * @return string
     */
    public function getValue($model)
    {
        if ($model->id > 0) {
            $valueType = $this->getType()->getValue($model);

            return $valueType;
        }

        return '<<' . $this->field . '>>';
    }

    /**
     * @return PdfLetterTrait
     */
    abstract public function getPdfLetter();

    /**
     * @param FPDI $pdf
     */
    protected function pdfPrepareFont($pdf)
    {
        $pdf->SetFont('Helvetica', '', $this->size, '', true);
    }

    /**
     * @param FPDI $pdf
     */
    protected function pdfPrepareColor($pdf)
    {
        list ($red, $green, $blue) = explode(',', $this->color);
        $pdf->SetTextColor(intval($red), intval($green), intval($blue));
    }

    /**
     * @param FPDI $pdf
     * @param $value
     * @return int|string
     */
    protected function pdfXPosition($pdf, $value)
    {
        switch ($this->align) {
            case 'center':
                $x = $this->x - ($pdf->GetStringWidth($value) / 2);
                break;
            case 'right':
                $x = $this->x - $pdf->GetStringWidth($value);
                if ($x < 0) {
                    $x = 0;
                }
                break;
            case 'left':
            default:
                $x = $this->x;
        }

        return $x;
    }
}
