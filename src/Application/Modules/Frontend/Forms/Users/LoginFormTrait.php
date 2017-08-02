<?php

namespace ByTIC\Common\Application\Modules\Frontend\Forms\Users;

/**
 * Class LoginFormTrait
 * @package ByTIC\Common\Application\Modules\Frontend\Forms\Users
 */
trait LoginFormTrait
{
    public function init()
    {
        parent::init();

        $this->addClass('box', 'user-login');

        $this->_trigger->setValue('login');

        $this->addInput('email', translator()->translate('email'), true)
            ->addPassword('password', translator()->translate('password'), true);

        $this->addButton('save', translator()->translate('signin'));
    }

    public function processValidation()
    {
        parent::processValidation();

        $email = $this->getElement('email');
        if (!$email->isError()) {
            $value = $email->getValue();
            if (!valid_email($value)) {
                $email->addError($this->getModelMessage('email.bad'));
            }
        }

        $password = $this->getElement('password');
        if (!$email->isError() && !$password->isError()) {
            $model = $this->getModel();
            $request = ['email' => $email->getValue(), 'password' => $password->getValue()];
            if (!$model->authenticate($request)) {
                $email->addError($this->getModelMessage('login.error'));
            }
        }
    }

    public function process()
    {
        return true;
    }

    protected function getDataFromModel()
    {
        parent::getDataFromModel();
        $this->_addModelFormMessage('no-email', 'email.empty')->_addModelFormMessage('no-password', 'password.empty');
    }
}
