<?php

namespace ByTIC\Common\Tests\Page\AbstractTraits;

/**
 * Class TableTrait
 * @package ByTIC\Common\TestsPage\AbstractTraits
 */
trait TableTrait
{

    protected $linkPath = null;

    protected $tablePath = null;

    protected $tableLinks = null;

    public function checkTable()
    {
        $this->getTester()->seeElement(['css' => $this->getTablePath()]);

        $links = $this->getTableLinks();
        $this->getTester()->assertGreaterThanOrEqual(1, count($links), "Check at least 1 table item defined");
    }

    /**
     * @return \ByTIC\Common\Tests\AcceptanceTester;
     */
    abstract protected function getTester();

    /**
     * @return null
     */
    public function getTablePath()
    {
        if (!$this->tablePath) {
            $this->getTester()->fail('table path must be set for ['.get_class($this).']');
        }

        return $this->tablePath;
    }

    /**
     * @return null
     */
    public function getTableLinks()
    {
        if ($this->tableLinks === null) {
            $this->initTableLinks();
        }

        return $this->tableLinks;
    }

    public function initTableLinks()
    {
        $this->tableLinks = $this->getTester()->grabMultiple($this->getLinkPath(), 'href');
    }

    /**
     * @return null
     */
    public function getLinkPath()
    {
        if (!$this->linkPath) {
            $this->getTester()->fail('links path must be set for ['.get_class($this).']');
        }

        return $this->linkPath;
    }

    /**
     * @return string
     */
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
