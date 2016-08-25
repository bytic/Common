<?php

namespace ByTIC\Common\Records\Traits;

use Nip\Cookie\Jar as CookieJar;
use Nip\Records\Record as Record;

/**
 * Class PersistentCurrent
 * @package ByTIC\Common\Records\Traits
 *
 *
 * @method Record findOne($ID)
 * @method string getTable()
 */
trait PersistentCurrent
{
    /**
     * @var Record
     */
    protected $_current;

    /**
     * @return Record
     */
    public function getCurrent()
    {
        if ($this->_current === null) {
            $this->_current = false;

            $item = $this->getFromSession();
            if (!$item) {
                $item = $this->getFromCookie();
            }

            if ($item && $this->checkAccessCurrent($item)) {
                $this->_current = $item;
            }
        }

        return $this->_current;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function setCurrent($item = false)
    {
        $varName = $this->getCurrentVarName();
        if (is_object($item)) {
            $_SESSION[$varName] = $item->toArray();
            CookieJar::instance()->newCookie()->setName($varName)->setValue($item->id)->save();
        } else {
            unset($_SESSION[$varName]);
            CookieJar::instance()->newCookie()->setName($varName)->setValue(0)->setExpire(time() - 1000)->save();
        }

        return $this;
    }

    public function getFromSession()
    {
        $varName = $this->getCurrentVarName();

        $sessionInfo = $_SESSION[$varName];
        if (is_array($sessionInfo)) {
            if ($sessionInfo['id']) {
                $ID = intval($sessionInfo['id']);
                return $this->findOne($ID);
            }
        }
        return false;
    }

    public function getCurrentVarName()
    {
        return $this->getTable();
    }

    public function getFromCookie()
    {
        $varName = $this->getCurrentVarName();
        if ($_COOKIE[$varName]) {
            $id = $_COOKIE[$varName];

            $item = $this->findOne(intval($id));
            if ($item) {
                return $item;
            }
        }
        return false;
    }

    public function checkAccessCurrent($item)
    {
        return is_object($item);
    }
}