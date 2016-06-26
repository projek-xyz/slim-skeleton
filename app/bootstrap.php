<?php

define('APP_DIR',     __DIR__.'/');
define('ROOT_DIR',    dirname(APP_DIR).'/');
define('ASSET_DIR',   ROOT_DIR.'asset/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('WWW_DIR',     ROOT_DIR.'public/');

use App\Handlers;
use Slim\Container;

// Loading vendors
require __DIR__.'/../vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

// Initialize Slim\App
$app = new Slim\App([
    'settings' => require_once __DIR__.'/settings.php'
]);

/** @var  Container  $container */
$container = $app->getContainer();

/** @var  array  $settings */
$settings = $container->get('settings');

// Get detailed information while not in production
if ($settings['mode'] !== 'production') {
    $settings['displayErrorDetails'] = true;
}

// Let's set default timezone
if (isset($settings['timezone'])) {
    date_default_timezone_set($settings['timezone'] ?: 'UTC');
}

// Let's just use PHP Native sesion
if (!isset($_SESSION)) {
    session_name($settings['basename']);
    session_start();
}

/**
 * Registering all defined providers
 */
foreach ($settings['providers'] as $provider) {
    $container->register(new $provider);
}

return $app;
