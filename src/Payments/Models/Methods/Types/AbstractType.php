<?php

namespace ByTIC\Common\Payments\Models\Methods\Types;

use \ByTIC\Models\SmartProperties\Properties\Types\Generic;

/**
 * Class AbstractType
 * @package ByTIC\Common\Payments\Models\Methods\Types
 */
abstract class AbstractType extends Generic
{

    protected $message;

    /**
     * @return bool|string
     */
    public function getEntryDescription()
    {
        return null;
    }

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
