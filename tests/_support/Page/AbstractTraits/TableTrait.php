<?php

namespace ByTIC\Common\Tests\Page\AbstractTraits;

/**
 * Class TableTrait
 * @package ByTIC\Common\TestsPage\AbstractTraits
 */
trait TableTrait
{

    protected $_tableLinks = null;

    public function checkTable()
    {
        $this->getTester()->seeElement(['css' => $this->getTablePath()]);

        $links = $this->getTableLinks();
        $this->getTester()->assertGreaterThanOrEqual(1, count($links), "Check at least 1 table item defined");
    }

    /**
     * @return \KM42\Register\Tests\AcceptanceTester;
     */
    abstract protected function getTester();

    public function getTablePath()
    {
        if (!$this->_tablePath) {
            $this->getTester()->fail('table path must be set for ['.get_class($this).']');
        }

        return $this->_tablePath;
    }

    public function getTableLinks()
    {
        if ($this->_tableLinks === null) {
            $this->initTableLinks();
        }

        return $this->_tableLinks;
    }

    public function initTableLinks()
    {
        $this->_tableLinks = $this->getTester()->grabMultiple($this->getLinkPath(), 'href');
    }

    public function getLinkPath()
    {
        if (!$this->_linkPath) {
            $this->getTester()->fail('links path must be set for ['.get_class($this).']');
        }

        return $this->_linkPath;
    }

    public function getFullLinkPath()
    {
        return $this->getTablePath().' '.$this->getLinkPath();
    }

    public function clickTableFirstLink()
    {
        $links = $this->getTableLinks();
        $link = reset($links);
        $this->getTester()->amOnUrl($link);
    }

}