<?php

namespace ByTIC\Common\Records\Traits\AbstractTrait;

use Nip\Records\_Abstract\Table as RecordManager;

trait RecordTrait
{

    /**
     * @return RecordManager
     */
    abstract public function getManager();


    /**
     * @return \Nip_Registry
     */
    abstract public function getRegistry();

}