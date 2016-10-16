<?php

namespace ByTIC\Common\Records\Users\Traits\AbstractUser;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;
use ByTIC\Common\Records\Users\Traits\Authentication\AuthenticationUsersTrait;

/**
 * Class AbstractUsersTrait
 * @package ByTIC\Common\Records\Users\AbstractUser
 */
trait AbstractUsersTrait
{
    use AbstractRecordsTrait;
    use AuthenticationUsersTrait;
}
