<?php

namespace ByTIC\Common\Records\Users\Traits\AbstractUser;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait as AbstractRecordTrait;
use ByTIC\Common\Records\Users\Traits\Authentication\AuthenticationUserTrait;

/**
 * Class AbstractUserTrait
 * @package ByTIC\Common\Records\Users\AbstractUser
 *
 * @property string $created
 */
trait AbstractUserTrait
{
    use AbstractRecordTrait;
    use AuthenticationUserTrait;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return mixed
     */
    public function insert()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        $this->created = date(DATE_DB);

        /** @noinspection PhpUndefinedClassInspection */
        return parent::insert();
    }
}
