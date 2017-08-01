<?php

define('PROJECT_BASE_PATH', __DIR__ . '/..');
define('TEST_BASE_PATH', __DIR__);
define('TEST_FIXTURE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

app()->share('inflector', new Nip\Inflector\Inflector());

require dirname(__DIR__) . '/vendor/autoload.php';
