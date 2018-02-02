<?php

namespace ByTIC\Common\Application\Models\Users\Traits;

use ByTIC\Common\Application\Models\Users\Traits\Authentication\AuthenticationUserTrait;
use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasForms;
use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaGenericTrait;
use ByTIC\Common\Records\Traits\Media\Logos\RecordTrait as MediaLogosTrait;

/**
 * Class AbstractUserTrait
 * @package ByTIC\Common\Application\Models\Users\Traits
 */
trait AbstractUserTrait
{
    use MediaGenericTrait;
    use MediaLogosTrait;
    use AuthenticationUserTrait;
    use HasForms;

    protected $logoTypes = ['listing'];

    /**
     * Get User Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
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

    /**
     * @return mixed
     */
    public function sendPasswordMail()
    {
        $email = new \User_Email_Recover();
        $email->setItem($this);

        return $email->save();
    }

    public function update()
    {
        $this->modified = date(DATE_DB);
        return parent::update();
    }
}
