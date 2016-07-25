<?php

namespace ByTIC\Common\Records\Traits;

trait PersistentCurrent
{
    /**
     * @return \Organizer
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

    public function checkAccessCurrent($item)
    {
        return true;
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

    public function setCurrent($item = false)
    {
        $varName = $this->getCurrentVarName();
        if (is_object($item)) {
            $_SESSION[$varName] = $item->toArray();
            \Nip_Cookie_Jar::instance()->newCookie()->setName($varName)->setValue($item->id)->save();
        } else {
            unset($_SESSION[$varName]);
            \Nip_Cookie_Jar::instance()->newCookie()->setName($varName)->setValue(0)->setExpire(time() - 1000)->save();
        }

        return $this;
    }

    public function getCurrentVarName()
    {
        return $this->getTable();
    }
}