<?php


namespace ByTIC\Common\Records\Traits\I18n;

use Nip\I18n\Translatable\HasTranslations;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\I18n
 */
trait RecordsTrait
{
    use HasTranslations;

    /**
     * @return string
     */
    public function getTranslateRoot()
    {
        return $this->getController();
    }
}
