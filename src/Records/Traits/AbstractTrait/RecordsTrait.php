<?php

namespace ByTIC\Common\Records\Traits\AbstractTrait;

use Nip\Records\Record;

trait RecordsTrait
{

    /**
     * @return Record
     */
    abstract public function getNew();


    /**
     * @return string
     */
    abstract public function getController();


    /**
     * @return string
     */
    abstract public function getRootNamespace();


}