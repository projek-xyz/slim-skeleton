<?php

define('ROOT_DIR',  dirname(__DIR__).'/');
define('APP_DIR',   __DIR__.'/');
define('ASSET_DIR', ROOT_DIR.'asset/');

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

// Initialize Slim\App
return new Slim\App([
    'settings' => require_once APP_DIR.'settings.php'
]);
