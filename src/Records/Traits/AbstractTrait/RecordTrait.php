<?php

namespace ByTIC\Common\Records\Traits\AbstractTrait;

use Nip\Records\AbstractModels\RecordManager;
use Nip_Registry;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\AbstractTrait
 */
trait RecordTrait
{

    /**
     * @return RecordManager
     */
    abstract public function getManager();

    /**
     * @param RecordManager|RecordsTrait $manager
     * @return $this
     */
    abstract public function setManager($manager);

    /**
     * @return Nip_Registry
     */
    abstract public function getRegistry();
}
