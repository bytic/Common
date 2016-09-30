<?php

namespace ByTIC\Common\Tests\Page\Acceptance;

/**
 * Class AbstractPage
 * @package KM42\Register\Tests\Page\Acceptance
 */
abstract class AbstractPage
{

    public static $URL = null;

    // incl_Abstractude url of current page
    public static $basePath = '';
    protected $acceptanceTester;

    /**
     * AbstractPage constructor.
     * @param $I
     */
    public function __construct($I)
    {
        $this->acceptanceTester = $I;

        $this->init();
    }

    protected function init()
    {
    }

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function haveErrorMessage()
    {
        $this->getTester()->see('', 'div.alert-danger');
    }

    /**
     * @return
     */
    protected function getTester()
    {
        return $this->acceptanceTester;
    }

    public function testPage()
    {
        $this->loadPage();
        $this->checkPage();
    }

    public function loadPage()
    {
        $this->getTester()->amOnPage(self::getURL());
    }

    public function checkPage()
    {
        $this->checkOnURL();
        $this->checkElements();
    }

    public function checkOnURL()
    {
        $I = $this->getTester();

        $pageURI = self::getURL();
        $browserURI = $I->getCurrentUri();
        $I->comment(" compare page [".$pageURI."][".$browserURI."]");
        if (strlen($pageURI) == strlen($browserURI)) {
            $I->seeCurrentUrlEquals($pageURI);
        } else {
            $I->seeCurrentUrlMatches('~'.preg_quote($pageURI).'~');
        }

        return $this;
    }

    public static function getURL()
    {
        return static::$basePath.static::$URL;
    }

    public function checkElements()
    {
    }

    public function addURLQueryParams($name, $value)
    {
        $urlParts = parse_url(static::$URL);
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $params);
        } else {
            $params = [];
        }
        $params[$name] = $value;
        static::$URL = $urlParts['path'].'?'.http_build_query($params);
    }
}
