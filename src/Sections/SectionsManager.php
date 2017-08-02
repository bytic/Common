<?php

namespace ByTIC\Common\Sections;

/**
 * Class Sections
 *
 */
class SectionsManager extends \Nip\Records\AbstractModels\RecordManager
{
    use \Nip\Utility\Traits\SingletonTrait;

    protected $_currentKey = null;

    /**
     * @return Section
     */
    public function getCurrent()
    {
        return $this->getOne($this->getCurrentKey());
    }

    /**
     * @param $key
     * @return Section
     */
    public function getOne($key)
    {
        $all = $this->getAll();
        return $all[$key];
    }

    /**
     * @return RecordCollection
     */
    public function getAll()
    {
        if (!$this->getRegistry()->exists('all')) {
            $this->getRegistry()->set('all', $this->initAll());
        }

        return $this->getRegistry()->get('all');
    }

    /**
     * @return array
     */
    public function initAll()
    {
        $colection = [];
        foreach ($data as $key => $row) {
            $colection[$key] = $this->getNewRecord($row);
        }
        return $colection;
    }

    /**
     * @return null
     */
    public function getCurrentKey()
    {
        if ($this->_currentKey === null) {
            $this->setCurrentKey($this->detectCurrentKey());
        }

        return $this->_currentKey;
    }

    /**
     * @param $key
     */
    public function setCurrentKey($key)
    {
        $this->_currentKey = $key;
    }

    /**
     * @return string
     */
    public function detectCurrentKey()
    {
        $current = $this->detectFromConstant();
        if (!$current) {
            $current = $this->detectFromSubdomain();
            if (!$current) {
                $current = 'www';
            }
        }

        return $current;
    }

    /**
     * @return bool|string
     */
    public function detectFromConstant()
    {
        return (defined('SPORTIC_SECTION')) ? SPORTIC_SECTION : false;
    }

    /**
     * @return bool|mixed
     */
    public function detectFromSubdomain()
    {
        return \Nip\Request::instance()->getHttp()->getSubdomain();
    }
}
