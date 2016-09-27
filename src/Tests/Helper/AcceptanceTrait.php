<?php

namespace ByTIC\Common\Tests\Helper;

use Codeception\Util\Fixtures;
use RuntimeException;

/**
 * Class AcceptanceTrait
 * @package ByTIC\Common\Tests\Helper
 */
trait AcceptanceTrait
{
    /**
     * @param $query
     * @return mixed
     */
    public function fetchOneFromQuery($query)
    {
        $result = $this->runSqlQuery($query);

        return $result->fetch();

        return $result->fetchAll();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function runSqlQuery($query)
    {
        $dbh = $this->getModule("Db")->dbh;

        return $dbh->query($query);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getCurrentUriParam($name)
    {
        $uri = $this->getCurrentUri();
        $query = parse_url($uri, PHP_URL_QUERY);
        parse_str($query, $params);

        return $params[$name];
    }

    /**
     * @return mixed
     */
    public function getCurrentUri()
    {
        return $this->getModule('PhpBrowser')->_getCurrentUri();
    }

    /**
     * @param $name
     * @param $value
     */
    public function pushFixture($name, $value)
    {
        if ($this->hasFixture($name)) {
            $array = $this->getFixture($name);
            $array = is_array($array) ? $array : [$array];
        } else {
            $array = [];
        }
        $array[] = $value;
        $this->addFixture($name, $array);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasFixture($name)
    {
        try {
            Fixtures::get($name);
        } catch (RuntimeException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFixture($name)
    {
        return Fixtures::get($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function addFixture($name, $value)
    {
        Fixtures::add($name, $value);
    }
}
