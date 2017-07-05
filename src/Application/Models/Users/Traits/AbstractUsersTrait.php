<?php

namespace ByTIC\Common\Application\Models\Users\Traits;

use ByTIC\Common\Application\Models\Users\Traits\Authentication\AuthenticationUsersTrait;
use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;

/**
 * Class AbstractUsersTrait
 * @package ByTIC\Common\Records\Users\AbstractUser
 */
trait AbstractUsersTrait
{
    use AbstractRecordsTrait;
    use AuthenticationUsersTrait;
}
