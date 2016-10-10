<?php

// Loading vendors
require __DIR__.'/../vendor/autoload.php';

define('FIXTURES_DIR', __DIR__.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR);

if (file_exists($env = __DIR__.'/.env') && is_file($env)) {
    (new Dotenv\Dotenv(dirname($env)))->load();
}
