<?php
namespace ByTIC\Common\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

/**
 * Class Acceptance
 * @package ByTIC\Common\Tests\Helper
 */
class Acceptance extends \Codeception\Module
{
    use AcceptanceTrait;
}
