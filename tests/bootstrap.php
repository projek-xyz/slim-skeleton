<?php

// Loading vendors
require __DIR__.'/../vendor/autoload.php';

foreach ([dirname(__DIR__).'/.env', __DIR__.'/.env'] as $env) {
    if (!file_exists($env)) {
        continue;
    }

    (new Dotenv\Dotenv(dirname($env)))->load();
}
