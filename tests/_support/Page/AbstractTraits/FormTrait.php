<?php

namespace ByTIC\Common\Tests\Page\AbstractTraits;

/**
 * Class FormTrait
 * @package KM42\Main\Tests\Page\AbstractTraits
 */
trait FormTrait
{

    protected $forms = [];

    protected $formNameDefault = 'default';

    protected $formData = [];

    public function checkForm($name = null)
    {
        $name = $this->initFormName($name);
        $form = $this->forms[$name];

        $this->getTester()->seeElement($form['path']);

        $fields = $form['fields'];
        foreach ($fields as $field => $path) {
            $this->getTester()->seeElement($path);
        }
    }

    public function initFormName($name = null)
    {
        return $name ? $name : $this->getFormNameDefault();
    }

    public function getFormNameDefault()
    {
        return $this->formNameDefault;
    }

    /**
     * @return \KM42\Register\Tests\AcceptanceTester;
     */
    abstract protected function getTester();

    public function submitForm($name = null)
    {
        $name = $this->initFormName($name);
        $form = $this->forms[$name];
        $this->getTester()->click($form['path'].' '.$form['submit']);
    }

    public function setFieldFormData($name, $value)
    {
        $this->formData[$name] = $value;
    }

    /**
     * sets a field value from data object
     * @param $name
     */
    public function setFieldFromData($name)
    {
        $value = $this->getFieldFormData($name);

        return $this->setFieldValue($name, $value);
    }

    public function getFieldFormData($name)
    {
        return isset($this->formData[$name]) ? $this->formData[$name] : null;
    }

    /**
     * Sets a form field value based on field type
     * @param $field
     * @param $value
     */
    public function setFieldValue($field, $value)
    {
        $type = $this->getFieldFormType($field);
        switch ($type) {
            case 'select':
                return $this->selectOptionForm($field, $value);
            case 'checkbox':
            case 'checkboxGroup':
                return $this->checkOptionForm($field);
            default:
                return $this->fillFieldForm($field, $value);
        }

    }

    public function getFieldFormType($field, $form = null)
    {
        return $this->getFieldFormParam($field, 'type', $form);
    }

    public function getFieldFormParam($field, $param, $form = null)
    {
        $form = $this->initFormName($form);

        return $this->forms[$form]['fields'][$field][$param];
    }

    public function selectOptionForm($field, $value, $form = null)
    {
        $form = $this->initFormName($form);
        $path = $this->getFieldFormPath($field, $form);
        $this->getTester()->selectOption($path, $value);
    }

    public function getFieldFormPath($field, $form = null)
    {
        return $this->getFieldFormParam($field, 'path', $form);
    }

    public function checkOptionForm($field, $form = null)
    {
        $form = $this->initFormName($form);
        $path = $this->getFieldFormPath($field, $form);
        $this->getTester()->checkOption($path);
    }

    public function fillFieldForm($field, $value, $form = null)
    {
        $form = $this->initFormName($form);
        $path = $this->getFieldFormPath($field, $form);
        $this->getTester()->fillField($path, $value);
    }

    protected function addForm($path, $name = null)
    {
        $name = $this->initFormName($name);
        $this->initForm($name);
        $this->forms[$name]['path'] = $path;
    }

    /**
     * @param null $name
     * @return $this
     */
    protected function initForm($name = null)
    {
        $name = $this->initFormName($name);
        if (!isset($this->forms[$name]) || !is_array($this->forms[$name])) {
            $this->forms[$name] = [
                'path' => 'form',
                'fields' => [],
                'submit' => 'button[type=submit]',
            ];
        }

        return $this;
    }

    protected function addInputForm($field, $params, $name = null)
    {
        return $this->addFieldForm($field, 'input', $params, $name);
    }

    /**
     * @param $field
     * @param $type
     * @param $params
     * @param null $name
     */
    protected function addFieldForm($field, $type, $params, $name = null)
    {
        $name = $this->initFormName($name);
        $this->initForm($name);

        $params = is_string($params) ? ['path' => $params] : $params;
        $params['type'] = $type;
        $this->forms[$name]['fields'][$field] = $params;
    }

    protected function addSelectForm($field, $params, $name = null)
    {
        return $this->addFieldForm($field, 'select', $params, $name);
    }

    protected function addRadioForm($field, $params, $name = null)
    {
        return $this->addFieldForm($field, 'radio', $params, $name);
    }

    protected function addCheckboxForm($field, $params, $name = null)
    {
        return $this->addFieldForm($field, 'checkbox', $params, $name);
    }

    protected function addCheckboxGroupForm($field, $params, $name = null)
    {
        return $this->addFieldForm($field, 'checkboxGroup', $params, $name);
    }
}
