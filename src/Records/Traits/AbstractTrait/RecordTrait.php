<?php

namespace ByTIC\Common\Records\Traits\AbstractTrait;

use Nip\Records\AbstractModels\RecordManager;
use Nip_Registry;

trait RecordTrait
{

    /**
     * @return RecordManager
     */
    abstract public function getManager();


    /**
     * @return Nip_Registry
     */
    abstract public function getRegistry();

}