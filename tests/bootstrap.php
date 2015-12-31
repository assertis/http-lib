<?php

namespace Assertis\Tests;

$possibleAutoloadFiles = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../../autoload.php',
];

foreach ($possibleAutoloadFiles as $possibleAutoloadFile) {
    if (file_exists($possibleAutoloadFile)) {
        $loader = require $possibleAutoloadFile;
        $loader->addPsr4('Assertis\\Tests\\', __DIR__.'\Assertis\Tests');
    }
}

error_reporting(E_ALL);
