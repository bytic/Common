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
 * @property string $id_letter
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
     * @param PdfLetterTrait $letter
     */
    public function populateFromLetter($letter)
    {
        $this->id_letter = $letter->id;
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
        $y = $this->pdfYPosition($pdf, $value);
        $pdf->Text($x, $y, $value);
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

        return '<<'.$this->field.'>>';
    }

    /**
     * @return PdfLetterTrait
     */
    abstract public function getPdfLetter();

    /**
     * @return bool
     */
    public function hasColor()
    {
        return substr_count($this->color, ',') == 2;
    }

    /**
     * @return array|null
     */
    public function getColorArray()
    {
        if ($this->hasColor()) {
            list ($red, $green, $blue) = explode(',', $this->color);
            if ($red && $green && $blue) {
                return [intval($red), intval($green), intval($blue)];
            }
        }

        return null;
    }

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
        $pdf->SetTextColor(0, 0, 0);
        $colors = $this->getColorArray();
        if (is_array($colors)) {
            $pdf->SetTextColor($colors);
        }
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

    /**
     * @param FPDI $pdf
     * @param $value
     * @return int|string
     */
    protected function pdfYPosition($pdf, $value)
    {
        $y = $this->y;
        $page = 1;
        if ($y > 297) {
            $page = round($y / 297, 0);
            $y -= ($page * 297);
            $page++;
        }

        if ($pdf->getPage() != $page && $page <= $pdf->getNumPages()) {
            $pdf->setPage($page);
        }

        return $y;
    }
}
