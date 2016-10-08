<?php

/** @define "ROOT_DIR" "../" */
define('ROOT_DIR', dirname(__DIR__).'/');

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

foreach ([ROOT_DIR.'.env', __DIR__.'/.env'] as $env) {
    if (!file_exists($env)) {
        continue;
    }

    (new Dotenv\Dotenv(dirname($env)))->load();
}
