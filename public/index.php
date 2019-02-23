<?php

use Hotel\HelloWorld;

require_once dirname(__DIR__) . '/vendor/autoload.php';

header('Cache-Control: no-cache');

phpinfo();

$helloWorld = new HelloWorld();
$helloWorld->announce();

echo '';