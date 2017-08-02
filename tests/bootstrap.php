<?php

define('PROJECT_BASE_PATH', __DIR__ . '/..');
define('TEST_BASE_PATH', __DIR__);
define('TEST_FIXTURE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

app()->share('inflector', new Nip\Inflector\Inflector());

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

\ByTIC\Common\Tests\Fixtures\Unit\Payments\Gateways\Providers\Mobilpay\MobilpayData::buildCertificates();

require dirname(__DIR__) . '/vendor/autoload.php';
