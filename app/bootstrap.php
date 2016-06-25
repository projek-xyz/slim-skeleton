<?php

define('APP_DIR',   __DIR__.'/');
define('ROOT_DIR',  dirname(APP_DIR).'/');
define('ASSET_DIR', ROOT_DIR.'asset/');
define('WWW_DIR',   ROOT_DIR.'public/');

// Loading vendors
require __DIR__.'/../vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

// Initialize Slim\App
return new Slim\App([
    'settings' => require_once __DIR__.'/settings.php'
]);
