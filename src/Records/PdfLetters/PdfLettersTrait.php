<?php

namespace ByTIC\Common\Records\PdfLetters;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;

/**
 * Class PdfLettersTrait
 * @package ByTIC\Common\Records\PdfLetters
 *
 * @method PdfLetterTrait[] findByParams($params)
 */
trait PdfLettersTrait
{
    use AbstractRecordsTrait;

    /**
     * @param $type
     * @param $idItem
     * @return bool|PdfLetterTrait
     */
    public function getByItem($type, $idItem)
    {
        $diplomas = $this->findByParams(
            [
                'where' => [
                    ['id_item = ?', $idItem],
                    ['type = ?', $type],
                ],
                'order' => [['id', 'DESC']],
            ]
        );

        $diploma = false;

        if (count($diplomas)) {
            foreach ($diplomas as $item) {
                if ($item->hasFile() && !$diploma) {
                    $diploma = $item;
                } else {
                    $item->delete();
                }
            }
        }

        return $diploma;
    }

    protected function initRelations()
    {
        parent::initRelations();
        $this->initCustomFieldsRelation();
    }

    protected function initCustomFieldsRelation()
    {

        $this->hasMany('CustomFields', ['class' => 'Diploma_Fields']);
    }

    abstract protected function getCustomFieldsManagerClass();

}