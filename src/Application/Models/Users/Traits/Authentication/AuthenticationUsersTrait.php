<?php

namespace ByTIC\Common\Application\Models\Users\Traits\Authentication;

use ByTIC\Common\Application\Models\Users\Traits\Authentication\AuthenticationUserTrait as User;
use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait as AbstractRecordsTrait;
use ByTIC\Common\Records\Traits\PersistentCurrent;
use Nip\Cookie\Jar as CookieJar;
use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Class AuthenticationUsersTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 * @method User findOneByEmail($email)
 */
trait AuthenticationUsersTrait
{
    use PersistentCurrent;
    use AbstractRecordsTrait;


    /**
     * @param User $item
     */
    public function beforeSetCurrent($item)
    {
        $item->authenticated(true);
    }

    /**
     * @return \Nip\Records\Record|User
     */
    public function getCurrentDefault()
    {
        return $this->getNew();
    }

    /**
     * @return bool|\Nip\Records\Record|User
     */
    public function getFromCookie()
    {
        if ($_COOKIE['login']) {
            list($id, $password) = explode(":", $_COOKIE['login']);

            /** @var User $user */
            $user = $this->findOne(intval($id));
            if ($user) {
                if ($user->checkSaltedPassword($password)) {
                    return $user;
                }
            }
        }

        return false;
    }

    /**
     * @param User $user
     */
    public function savePersistCurrentSession($user)
    {
        $varName = $this->getCurrentVarName();
        $data = $user->toArray();
        unset($data['_forms']);
        unset($data['password_repeat']);
        $_SESSION[$varName] = $data;
    }

    /**
     * @param User $user
     */
    public function savePersistCurrentCookie($user)
    {
        $varName = $this->getCurrentVarName();
        $helper = $this->getPasswordHelper()->setSalt($user->salt);
        $value = $user->id.':'.$helper->hash($user->password);
        CookieJar::instance()->newCookie()->setName($varName)->setValue($value)->save();
    }


    /**
     * @return PasswordsHelper
     */
    public function getPasswordHelper()
    {
        return HelperBroker::instance()->getByName('Passwords');
    }
}
