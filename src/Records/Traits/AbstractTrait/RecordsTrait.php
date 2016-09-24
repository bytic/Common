<?php

namespace ByTIC\Common\Records\Traits\AbstractTrait;

use Nip\Records\Record;

trait RecordsTrait
{

    /**
     * @param array $data [optional]
     * @return Record
     */
    abstract public function getNew($data = array());


    /**
     * @return string
     */
    abstract public function getController();

    /**
     * @return string
     */
    abstract public function getModel();

    /**
     * @return string
     */
    abstract public function getTable();

    /**
     * @return string
     */
    abstract public function getRootNamespace();

    /**
     * @return string
     */
    abstract public function getModelNamespace();
}