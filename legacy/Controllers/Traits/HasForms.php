<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasFormsRecord;
use Nip_Form_Model as Form;

/**
 * Trait HasForms
 * @package ByTIC\Common\Controllers\Traits
 *
 * @deprecated use \ByTIC\Controllers\Behaviors\HasCacheManager;
 */
trait HasForms
{
    use \ByTIC\Controllers\Behaviors\HasForms;
}