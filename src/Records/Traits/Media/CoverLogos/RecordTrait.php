<?php

namespace ByTIC\Common\Records\Traits\Media\CoverLogos;

use ByTIC\Common\Records\Traits\Media\Covers\RecordTrait as CoversTrait;
use ByTIC\Common\Records\Traits\Media\Logos\RecordTrait as LogosTrait;

trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    use CoversTrait, LogosTrait {
        CoversTrait::getUploadPath insteadof LogosTrait;
        CoversTrait::getUploadURL insteadof LogosTrait;
        CoversTrait::getImageBasePath insteadof LogosTrait;
        CoversTrait::getImageBaseURL insteadof LogosTrait;
        CoversTrait::getImageURL insteadof LogosTrait;
        CoversTrait::getImagePath insteadof LogosTrait;
    }

}