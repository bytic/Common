<?php

namespace ByTIC\Common\Records\Media\Traits;

use ByTIC\Common\Records\Record;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait;

/**
 * Trait HasModels
 * @package ByTIC\Common\Records\Media\Traits
 */
trait HasModels
{

    /**
     * The model instance
     *
     * @var Record
     */
    protected $model;


    /**
     * @return Record|RecordTrait
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set Model
     *
     * @param Record|RecordTrait $model
     *
     * @return $this
     */
    public function setModel(Record $model)
    {
        $this->model = $model;

        return $this;
    }
}