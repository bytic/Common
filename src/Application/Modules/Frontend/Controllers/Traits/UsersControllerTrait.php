<?php

namespace ByTIC\Common\Application\Modules\Frontend\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Controllers\Traits\HasUser;

/**
 * Class UsersControllerTrait
 * @package ByTIC\Common\Application\Modules\Frontend\Controllers\Traits
 */
trait UsersControllerTrait
{
    use AbstractControllerTrait;
    use HasUser;

    public function login()
    {
        $loginForm = $this->_getUser()->getForm('login');

        if ($loginForm->submited()) {
            if ($loginForm->processRequest()) {
                $this->_loginRedirect();
            }
        } else {
            if ($_GET['message']) {
                $loginForm->addMessage($_GET['message'], 'info');
            }
        }

        $this->forms['login'] = $loginForm;
        $this->_setMeta('login');

        $this->getView()->Breadcrumbs()->addItem(
            $this->getModelManager()->getLabel('login-title'),
            $this->_getUser()->getManager()->getLoginURL()
        );
        $this->getView()->Meta()->prependTitle($this->getModelManager()->getLabel('login-title'));

    }

    protected function _loginRedirect()
    {
        $redirect = $_GET['redirect'] ? urldecode($_GET['redirect']) : $this->Url()->default();
        $this->flashRedirect($this->getModelManager()->getMessage('login-success'), $redirect, 'success', 'index');
    }

    /**
     * @param $action
     */
    protected function _setMeta($action)
    {
        $label = $this->getModelManager()->getLabel($action . '-title');
        $urlMethod = 'get' . ucfirst($action) . 'URL';
        $this->getView()->Breadcrumbs()->addItem($label, $this->_getUser()->getManager()->$urlMethod());

        $this->getView()->Meta()->prependTitle($label);
    }

    public function loginWith()
    {
        $providerName = $_REQUEST["provider"];
        $redirect = $_GET['redirect'];

        $userProfile = APP_Hybrid_Auth::instance()->authenticate($providerName);

        if ($userProfile instanceof Exception) {
            $this->getView()->exception = $userProfile;
        } else {
            $userExist = User_Logins::instance()->getUserByProvider($providerName, $userProfile->identifier);

            if (!$userExist) {
                $this->redirect(Users::instance()->getOAuthLinkURL([
                    'provider' => $providerName,
                    'redirect' => $redirect
                ]));
            }

            $userExist->doAuthentication();

            $this->_loginRedirect();
        }
    }

    public function oAuth()
    {
        return Hybrid_Endpoint::process();
    }

    public function oAuthLink()
    {
        $providerName = $_REQUEST["provider"];
        $userProfile = APP_Hybrid_Auth::instance()->authenticate($providerName);

        $this->_getUser()->first_name = $userProfile->firstName;
        $this->_getUser()->last_name = $userProfile->lastName;
        $this->_getUser()->email = $userProfile->email;

        foreach (['login', 'register'] as $_action) {
            $form = $this->_getUser()->getForm($_action);

            if ($form->execute()) {
                $userLogin = User_Logins::instance()->getNew();
                $userLogin->id_user = $this->_getUser()->id;
                $userLogin->provider_name = $providerName;
                $userLogin->provider_uid = $userProfile->identifier;
                $userLogin->insert();

                $this->{'_' . $_action . 'Redirect'}();
            }
            $this->forms[$_action] = $form;
        }

        $this->_setMeta('login');
    }

    public function register()
    {
        $formRegister = $this->_getUser()->getForm('register');

        /** @var \Default_Forms_User_Register $formRegister */
        if ($formRegister->execute()) {
            $this->_registerRedirect();
        }
        $this->forms['register'] = $formRegister;
        $this->_setMeta('register');
    }

    protected function _registerRedirect()
    {
        $redirect = $_GET['redirect'] ? $_GET['redirect'] : $this->Url()->default();
        $thankYouURL = $this->getModelManager()->getRegisterThankYouURL(['redirect' => $redirect]);
        $this->flashRedirect($this->getModelManager()->getMessage('register-success'), $thankYouURL, 'success',
            'index');
    }

    public function registerThankYou()
    {
        $redirect = $_GET['redirect'] ? $_GET['redirect'] : $this->Url()->default();
        $this->getView()->set('redirect', $redirect);
    }

    public function recoverPassword()
    {
        $formsRecover = $this->_getUser()->getForm('recoverPassword');

        if ($formsRecover->execute()) {
            $redirect = $this->getModelManager()->getRecoverPasswordURL();
            $this->flashRedirect($this->getModelManager()->getMessage('recoverPassword.success'), $redirect);
        }

        $this->forms['recover'] = $formsRecover;
        $this->_setMeta('recoverPassword');
    }

    public function profile()
    {
        $formsProfile = $this->_getUser()->getForm('profile');

        if ($formsProfile->execute()) {
            $redirect = $_GET['redirect'] ? $_GET['redirect'] : Users::instance()->getProfileURL();
            $this->flashRedirect($this->getModelManager()->getMessage('profile.success'), $redirect);
        }
        $this->forms['profile'] = $formsProfile;
        $this->_setMeta('profile');
    }

    public function changePassword()
    {
        $formPassword = $this->_getUser()->getForm('password');

        if ($formPassword->execute()) {
            $redirect = $_GET['redirect'] ? $_GET['redirect'] : Users::instance()->getChangePasswordURL();
            $this->flashRedirect($this->getModelManager()->getMessage('password.change'), $redirect);
        }
        $this->forms['password'] = $formPassword;
        $this->_setMeta('changePassword');
    }

    public function setProfilePicture()
    {

        $item = $this->_getUser();
        $url = $this->getModelManager()->getSetProfilePictureURL();

        if ($_FILES['logo']) {
            $message = '';
            $mType = 'error';

            $type = $_REQUEST['type'];

            if (in_array($type, $item->getLogoTypes())) {
                $image = $item->uploadLogo($type, $_FILES['logo']);

                if (is_object($image)) {
                    $this->_response['type'] = 'success';
                    $this->_response['url'] = $image->getUrl();
                    $this->_response['path'] = $image->getPath();
                    $this->_response['imageType'] = $image->getImageType();
                    $message = Users::instance()->getMessage('profile.picture.add');
                    $mType = 'success';
                } else {
                    $message = $item->errors['upload'];
                }
            } else {
                $message = 'bad logo type';
            }

            $this->flashRedirect($message, $url, $mType);
        }

        $this->_setMeta('setProfilePicture');

    }

    public function logout()
    {
        $this->_getUser()->deauthenticate();
        $this->redirect($this->Url()->default());
    }

    protected function beforeAction()
    {
        parent::beforeAction();

        $this->_checkAuth();

        $this->getView()->StyleSheets()->add('users');
        $this->getView()->StyleSheets()->add('homepage');
    }

    protected function _checkAuth()
    {
        $noAuthFunctions = ['login', 'loginWith', 'oAuth', 'oAuthLink', 'register', 'recoverPassword'];

        if (!in_array($this->getAction(), $noAuthFunctions)) {
            $this->_checkUser();
        } else {
            if ($this->_getUser()->authenticated()) {
                header("Location: " . $this->Url()->default());
                exit();
            }
        }
    }

    protected function afterAction()
    {
        $this->setLayout('login');
        parent::afterAction();
    }
}