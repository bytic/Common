<?php

namespace ByTIC\Common\Records\PdfLetters;

use ByTIC\Common\Records\Records;
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
     * @param string $type
     * @param int $idItem
     * @return bool|PdfLetterTrait
     */
    public function getByItem($type, $idItem)
    {
        /** @var PdfLetterTrait[] $letters */
        $letter = $this->findByParams(
            [
                'where' => [
                    ['id_item = ?', $idItem],
                    ['type = ?', $type],
                ],
                'order' => [['id', 'DESC']],
            ]
        );

        $diploma = false;

        if (count($letter)) {
            foreach ($letter as $item) {
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
        $this->hasMany('CustomFields', $this->getCustomFieldsRelationParams());
    }

    /**
     * @return array
     */
    protected function getCustomFieldsRelationParams()
    {
        return [
            'class' => $this->getCustomFieldsManagerClass(),
            'fk' => 'id_letter'
        ];
    }

    /**
     * @return string
     */
    abstract protected function getCustomFieldsManagerClass();

    /**
     * @param $type
     * @return Records
     */
    abstract protected function getParentManagerFromType($type);
}
