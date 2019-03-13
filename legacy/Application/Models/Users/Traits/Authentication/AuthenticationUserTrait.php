<?php

namespace ByTIC\Common\Application\Models\Users\Traits\Authentication;

use ByTIC\Common\Application\Models\Users\Traits\AbstractUserTrait as User;
use ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait as AbstractRecordTrait;
use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Class AuthenticationUserTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 * @deprecated use \ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait;
 */
trait AuthenticationUserTrait
{
    use \ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait;
}
