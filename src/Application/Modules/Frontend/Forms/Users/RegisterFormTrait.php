<?php

namespace ByTIC\Common\Application\Modules\Frontend\Forms\Users;

/**
 * Class RegisterFormTrait
 * @package ByTIC\Common\Application\Modules\Frontend\Forms\Users
 */
trait RegisterFormTrait
{
    public function init()
    {
        parent::init();

        $this->addClass('box', 'user-register');

        $this->_trigger->setValue('register');

        $this->addInput('first_name', translator()->translate('first_name'), true)
            ->addInput('last_name', translator()->translate('last_name'), true)
            ->addInput('email', translator()->translate('email'), true)
            ->addPassword('password', translator()->translate('password'), true)
            ->addPassword('password_repeat', translator()->translate('password_repeat'), true);

        $this->addButton('save', translator()->translate('submit'));
    }

    public function processValidation()
    {
        parent::processValidation();

        $element = 'email';
        $formEl = $this->getElement($element);
        if (!$formEl->isError()) {
            $value = $formEl->getValue();
            if (!valid_email($value)) {
                $formEl->addError($this->getModelMessage($element . '.bad'));
            } else {
                $this->getModel()->email = $value;
                if ($this->getModel()->exists()) {
                    $formEl->addError($this->getModelMessage($element . '.exists'));
                }
            }
        }

        $password = $this->getElement('password');
        $password_repeat = $this->getElement('password_repeat');
        if (!$password->isError() && !$password_repeat->isError()) {
            if ($password->getValue() != $password_repeat->getValue()) {
                $password->addError($this->getModelMessage('password.match'));
            }
        }

    }

    public function process()
    {
        $this->saveToModel();

        $this->getModel()->register();
        $this->getModel()->doAuthentication();
        return true;
    }

    protected function getDataFromModel()
    {
        parent::getDataFromModel();
        $this->_addModelFormMessage('no-first_name', 'first_name.empty')
            ->_addModelFormMessage('no-last_name', 'last_name.empty')
            ->_addModelFormMessage('no-email', 'email.empty')
            ->_addModelFormMessage('no-password', 'password.empty')
            ->_addModelFormMessage('no-password_repeat', 'password_repeat.empty');
    }

}