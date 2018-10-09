<?php

namespace ByTIC\Common\Application\Models\Users\Traits;

use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasForms;

/**
 * Class AbstractUserTrait
 * @package ByTIC\Common\Application\Models\Users\Traits
 *
 * @deprecated Use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait
 */
trait AbstractUserTrait
{
    use \ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;
    use HasForms;

    protected $logoTypes = ['listing'];
}
