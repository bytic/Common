<?php

namespace ByTIC\Common\Records\Traits;

use Nip\Cookie\Jar as CookieJar;
use Nip\Records\Record as Record;

/**
 * Class PersistentCurrent
 *
 * @package ByTIC\Common\Records\Traits
 *
 * @method Record findOne($ID)
 * @method string getTable()
 */
trait PersistentCurrent
{
    /**
     * Persistant record
     *
     * @var Record
     */
    protected $current;

    /**
     * Get current Persisted record
     *
     * @return Record
     */
    public function getCurrent()
    {
        if ($this->current === null) {
            $this->current = false;

            $item = $this->getFromSession();
            if (!$item) {
                $item = $this->getFromCookie();
            }

            if ($item && $this->checkAccessCurrent($item)) {
                $this->beforeSetCurrent($item);
                $this->setAndSaveCurrent($item);
            } else {
                $this->setCurrent($this->getCurrentDefault());
            }
        }

        return $this->current;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function setCurrent($item = false)
    {
        $this->current = $item;

        return $this;
    }

    /**
     * Get persisted Record from Session
     *
     * @return bool|Record
     */
    public function getFromSession()
    {
        $sessionInfo = $this->getCurrentSessionData();

        if (is_array($sessionInfo)) {
            if (isset($sessionInfo['id']) && !empty($sessionInfo['id'])) {
                $recordId = intval($sessionInfo['id']);

                return $this->findOne($recordId);
            }
        }

        return false;
    }

    /**
     * Get session data for persistent record from session
     *
     * @return []|null
     */
    public function getCurrentSessionData()
    {
        $varName = $this->getCurrentVarName();

        return isset($_SESSION[$varName]) ? $_SESSION[$varName] : null;
    }

    /**
     * Get key for Session data
     *
     * @return string
     */
    public function getCurrentVarName()
    {
        return $this->getTable();
    }

    /**
     * @param $item
     * @return bool
     */
    public function checkAccessCurrent($item)
    {
        return is_object($item);
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function setAndSaveCurrent($item = false)
    {
        $this->setCurrent($item);
        $this->savePersistCurrent($item);

        return $this;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function savePersistCurrent($item)
    {
        if (is_object($item)) {
            $this->savePersistentCurrent($item);
        } else {
            $this->removePersistentCurrent();
        }

        return $this;
    }

    /**
     * Persist record
     *
     * @param Record|boolean $item record to be persisted
     *
     * @return $this
     */
    public function savePersistentCurrent($item)
    {
        $this->savePersistCurrentSession($item);
        $this->savePersistCurrentCookie($item);

        return $this;
    }

    /**
     * @return $this
     */
    public function removePersistentCurrent()
    {
        $varName = $this->getCurrentVarName();
        unset($_SESSION[$varName]);

        CookieJar::instance()
            ->newCookie()->setName($varName)
            ->setValue(0)
            ->setExpire(time() - 1000)->save();

        return $this;
    }

    /**
     * Returns current default Persisted Record
     *
     * @return bool|mixed
     */
    public function getCurrentDefault()
    {
        return false;
    }

    /**
     * @param Record $item
     */
    public function beforeSetCurrent($item)
    {
    }

    /**
     * Save record in session
     *
     * @param Record|boolean $item Record to save in session
     *
     * @return void
     */
    public function savePersistCurrentSession($item)
    {
        $varName = $this->getCurrentVarName();
        $_SESSION[$varName] = $item->toArray();
    }

    /**
     * Save record in Cookie
     *
     * @param Record|boolean $item Record to save in cookie
     *
     * @return void
     */
    public function savePersistCurrentCookie($item)
    {
        $varName = $this->getCurrentVarName();
        CookieJar::instance()->newCookie()
            ->setName($varName)
            ->setValue($item->id)
            ->save();
    }

    /**
     * Get persistent record from Cookie
     *
     * @return bool|Record
     */
    public function getFromCookie()
    {
        $varName = $this->getCurrentVarName();
        if ($_COOKIE[$varName]) {
            $recordId = $_COOKIE[$varName];

            $item = $this->findOne(intval($recordId));
            if ($item) {
                return $item;
            }
        }

        return false;
    }
}
