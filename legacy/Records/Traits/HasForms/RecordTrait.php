<?php

namespace ByTIC\Common\Records\Traits\HasForms;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait as AbstractTrait;
use Nip_Form_Model as Form;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\HasForms
 * @deprecated use ByTIC\Records\Behaviors\HasForms\HasFormsRecordsTrait
 */
trait RecordTrait
{
    use ByTIC\Records\Behaviors\HasForms\HasFormsRecordTrait;
}
