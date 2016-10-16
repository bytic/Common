<?php

namespace ByTIC\Common\Tests\Data\Unit\Records\Traits\HasSmartProperties\RegistrationStatuses;

/**
 * Class AbstractStatus
 * @package KM42\Register\Models\Events\Pacers\RegistrationStatuses
 */
abstract class AbstractStatus extends \ByTIC\Common\Records\Properties\Statuses\Generic
{

    /**
     * @return string
     */
    public function getColor()
    {
        return '#999';
    }
}
