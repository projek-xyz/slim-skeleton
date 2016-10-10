<?php

/** @define "ROOT_DIR" "../" */
define('ROOT_DIR', dirname(__DIR__).DIRECTORY_SEPARATOR);

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

foreach ([__DIR__.'/.env', ROOT_DIR.'.env'] as $env) {
    if (!file_exists($env)) {
        continue;
    }

    (new Dotenv\Dotenv(dirname($env)))->load();
}
