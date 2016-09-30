<?php

namespace ByTIC\Common\Payments\Methods\Traits;

use ByTIC\Common\Records\Traits\HasTypes\RecordsTrait as HasTypesRecordsTrait;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Payments\Methods\Traits
 */
trait RecordsTrait
{
    use HasTypesRecordsTrait;

    /**
     * @return string
     */
    public function getTypesDirectory()
    {
        return dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'Types';
    }

    /**
     * @return string
     */
    public function getTypeNamespace()
    {
        return '\ByTIC\Common\Payments\Methods\Types\\';
    }
}