<?php

namespace ByTIC\Common\Records\Properties\AbstractProperty\Traits;

use ByTIC\Common\Records\Traits\I18n\RecordsTrait as RecordsTranslated;
use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;
use ByTIC\Common\Records\Traits\HasTypes\RecordsTrait as HasTypesRecords;
use Nip\Records\RecordManager as Records;

/**
 * Trait HasManagerTrait
 * @package ByTIC\Common\Records\Properties\AbstractProperty\Traits
 */
trait HasManagerTrait
{

    /**
     * @var null|Records|RecordsTranslated|HasTypesRecords
     */
    protected $manager;


    /**
     * @return Records|RecordsTranslated|HasTypesRecords
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Records|RecordsTrait $manager
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasManager()
    {
        return $this->manager instanceof Records;
    }
}