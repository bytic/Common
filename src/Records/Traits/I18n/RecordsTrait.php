<?php


namespace ByTIC\Common\Records\Traits\I18n;

use Nip_I18n as Translator;

trait RecordsTrait
{
    /**
     * @var Translator
     */
    protected $_translator = null;

    public function getLabel($type, $params = array(), $language = false)
    {
        $slug = 'labels.' . $type;
        return $this->translate($slug, $params, $language);
    }

    public function getMessage($name, $params = array(), $language = false)
    {
        $slug = 'messages.' . $name;
        return $this->translate($slug, $params, $language);
    }

    public function translate($slug, $params = array(), $language = false)
    {
        $slug = $this->getController() . '.' . $slug;
        return $this->getTranslator()->translate($slug, $params, $language);
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        if ($this->_translator == null) {
            $this->initTranslator();
        }
        return $this->_translator;
    }

    protected function initTranslator()
    {
        $this->_translator = $this->newTranslator();
    }

    /**
     * @return Translator
     */
    protected function newTranslator()
    {
        return Nip_I18n::instance();
    }
}