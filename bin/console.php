#!/usr/bin/env php
<?php

use Common\ConsoleApplication;

require __DIR__ . '/../vendor/autoload.php';

$application = new ConsoleApplication();

// ... register commands

$application->run();