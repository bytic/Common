<?php


namespace ByTIC\Common\Records\Traits\I18n;

use Nip\I18n\Translator as Translator;

/**
 * Class RecordsTrait
 * @package ByTIC\Common\Records\Traits\I18n
 */
trait RecordsTrait
{
    /**
     * @var Translator
     */
    protected $translator = null;

    /**
     * @param $type
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function getLabel($type, $params = [], $language = false)
    {
        $slug = 'labels.'.$type;

        return $this->translate($slug, $params, $language);
    }

    /**
     * @param $slug
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function translate($slug, $params = [], $language = false)
    {
        $slug = $this->getController().'.'.$slug;

        return $this->getTranslator()->translate($slug, $params, $language);
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        if ($this->translator == null) {
            $this->initTranslator();
        }

        return $this->translator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    protected function initTranslator()
    {
        $this->setTranslator($this->newTranslator());
    }

    /**
     * @return Translator
     */
    protected function newTranslator()
    {
        return app('translator');
    }

    /**
     * @param $name
     * @param array $params
     * @param bool $language
     * @return string
     */
    public function getMessage($name, $params = [], $language = false)
    {
        $slug = 'messages.'.$name;

        return $this->translate($slug, $params, $language);
    }
}
