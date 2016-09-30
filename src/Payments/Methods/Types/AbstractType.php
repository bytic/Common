<?php

namespace ByTIC\Common\Payments\Methods\Types;

use ByTIC\Common\Records\Properties\Types\Generic;

/**
 * Class AbstractType
 * @package ByTIC\Common\Payments\Methods\Types
 */
abstract class AbstractType extends Generic
{

    protected $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if (!$this->message) {
            $this->message = $this->getManager()->getMessage('types.' . $this->getName());
        }
        return $this->message;
    }

    public function afterInsert()
    {
    }

    /**
     * @return bool
     */
    public function checkConfirmRedirect()
    {
        return false;
    }
}
