<?php

namespace ByTIC\Common\Application\Modules\Frontend\Controllers\Traits;

use APP_Hybrid_Auth;
use Nip\Controllers\Traits\AbstractControllerTrait;
use ByTIC\Common\Application\Models\Users\Traits\AbstractUserTrait;
use ByTIC\Common\Controllers\Traits\HasModels;
use Exception;
use Hybrid_Endpoint;
use Nip\Form\AbstractForm;
use User_Logins;

/**
 * Class UsersControllerTrait
 * @package ByTIC\Common\Application\Modules\Frontend\Controllers\Traits
 */
trait UsersControllerTrait
{
    use AbstractControllerTrait;
    use HasModels;

    public function login()
    {
        /** @var AbstractForm $loginForm */
        $loginForm = $this->_getUser()->getForm('login');

        if ($loginForm->submited()) {
            if ($loginForm->processRequest()) {
                $this->afterLoginRedirect();
            }
        } else {
            if ($_GET['message']) {
                $loginForm->addMessage($_GET['message'], 'info');
            }
        }

        $this->forms['login'] = $loginForm;
        $this->setLoginMeta('login');

        $this->getView()->Breadcrumbs()->addItem(
            $this->getModelManager()->getLabel('login-title'),
            $this->_getUser()->getManager()->getLoginURL()
        );

        $this->getView()->Meta()
            ->prependTitle($this->getModelManager()->getLabel('login-title'));
    }

    /**
     * @return AbstractUserTrait
     */
    abstract protected function _getUser();

    /**
     * Redirect after login
     */
    public function afterLoginRedirect()
    {
        $redirect = $this->getRequest()->query->get(
            'redirect',
            $this->Url()->default()
        );

        $this->flashRedirect(
            $this->getModelManager()->getMessage('login-success'),
            $redirect,
            'success',
            'index'
        );
    }

    /**
     * Set Login Meta
     *
     * @param string $action Action name
     *
     * @return void
     */
    protected function setLoginMeta($action)
    {
        $label = $this->getModelManager()->getLabel($action . '-title');
        $urlMethod = 'get' . ucfirst($action) . 'URL';
        $this->getView()->Breadcrumbs()
            ->addItem($label, $this->_getUser()->getManager()->$urlMethod());

        $this->getView()->Meta()->prependTitle($label);
    }

    /**
     *  Login with Auth Provider
     */
    public function loginWith()
    {
        $providerName = $_REQUEST["provider"];
        $redirect = $_GET['redirect'];

        $userProfile = $this->getAuthProvider()->authenticate($providerName);

        if ($userProfile instanceof Exception) {
            $this->getView()->set('exception', $userProfile);
        } else {
            $userExist = User_Logins::instance()->getUserByProvider($providerName, $userProfile->identifier);

            if (!$userExist) {
                $this->redirect(
                    $this->getModelManager()->getOAuthLinkURL([
                        'provider' => $providerName,
                        'redirect' => $redirect
                    ]));
            }

            $userExist->doAuthentication();

            $this->afterLoginRedirect();
        }
    }

    /**
     * @return APP_Hybrid_Auth
     */
    protected function getAuthProvider()
    {
        return APP_Hybrid_Auth::instance();
    }

    /**
     * Process OAuth request
     * @return mixed
     */
    public function oAuth()
    {
        return Hybrid_Endpoint::process();
    }

    /**
     *  Link an OAuth to existing user
     */
    public function oAuthLink()
    {
        $providerName = $_REQUEST["provider"];
        $userProfile = $this->getAuthProvider()->authenticate($providerName);

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


                $this->{'after' . ucfirst($_action) . 'Redirect'}();
            }
            $this->forms[$_action] = $form;
        }

        $this->setLoginMeta('login');
    }

    /**
     *  Register Action
     */
    public function register()
    {
        $formRegister = $this->_getUser()->getForm('register');

        /** @var \Default_Forms_User_Register $formRegister */
        if ($formRegister->execute()) {
            $this->afterRegisterRedirect();
        }
        $this->forms['register'] = $formRegister;
        $this->setLoginMeta('register');
    }

    protected function afterRegisterRedirect()
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
            $redirect = $this->getModelManager()->compileURL('recoverPassword');
            $this->flashRedirect($this->getModelManager()->getMessage('recoverPassword.success'), $redirect);
        }

        $this->forms['recover'] = $formsRecover;
        $this->setLoginMeta('recoverPassword');
    }

    /**
     * User Profile Page
     */
    public function profile()
    {
        $formsProfile = $this->_getUser()->getForm('profile');

        if ($formsProfile->execute()) {
            $redirect = $this->getRequest()->query->get(
                'redirect',
                $this->getModelManager()->getProfileURL()
            );
            $this->flashRedirect(
                $this->getModelManager()->getMessage('profile.success'),
                $redirect
            );
        }
        $this->forms['profile'] = $formsProfile;
        $this->setLoginMeta('profile');
    }

    public function changePassword()
    {
        $formPassword = $this->_getUser()->getForm('password');

        if ($formPassword->execute()) {
            $redirect = $this->getRequest()->query->get(
                'redirect',
                $this->getModelManager()->getChangePasswordURL()
            );
            $this->flashRedirect(
                $this->getModelManager()->getMessage('password.change'),
                $redirect
            );
        }
        $this->forms['password'] = $formPassword;
        $this->setLoginMeta('changePassword');
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
                    $message = $this->getModelManager()->getMessage('profile.picture.add');
                    $mType = 'success';
                } else {
                    $message = $item->errors['upload'];
                }
            } else {
                $message = 'bad logo type';
            }

            $this->flashRedirect($message, $url, $mType);
        }

        $this->setLoginMeta('setProfilePicture');
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

        $this->getView()->Stylesheets()->add('users');
        $this->getView()->Stylesheets()->add('homepage');
    }

    protected function _checkAuth()
    {
        $noAuthFunctions = [
            'login', 'loginWith',
            'oAuth', 'oAuthLink',
            'register', 'recoverPassword'
        ];

        if (!in_array($this->getAction(), $noAuthFunctions)) {
            $this->_checkUser();
        } else {
            if ($this->_getUser()->authenticated()) {
                header("Location: " . $this->Url()->default());
                exit();
            }
        }
    }

    /**
     * After Action
     *
     * @return void
     */
    protected function afterAction()
    {
        $this->setLayout('login');
        $this->initViewModelManager();
        parent::afterAction();
    }
}
