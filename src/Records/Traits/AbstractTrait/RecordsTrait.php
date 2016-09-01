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
    abstract public static function getRootNamespace();


}