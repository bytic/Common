<?php

namespace ByTIC\Common\Application\Models\Users\Traits;

use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaGenericTrait;
use ByTIC\Common\Records\Traits\Media\Logos\RecordTrait as MediaLogosTrait;

/**
 * Class AbstractUserTrait
 * @package ByTIC\Common\Application\Models\Users\Traits
 *
 * @deprecated use \ByTIC\Auth\Models\Users\Traits\AbstractUsersTrait; use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaGenericTrait; use ByTIC\Common\Records\Traits\Media\Logos\RecordTrait as MediaLogosTrait;
 */
trait AbstractUserTrait
{
    use MediaGenericTrait;
    use MediaLogosTrait;
    use \ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;

    protected $logoTypes = ['listing'];
}
