<?php
// This is global bootstrap for autoloading

\Codeception\Util\Autoload::addNamespace('ByTIC\Common\Tests\Data', \Codeception\Configuration::dataDir());
\Codeception\Util\Autoload::addNamespace('ByTIC\Common\Tests\Unit', \Codeception\Configuration::testsDir().'\unit');
