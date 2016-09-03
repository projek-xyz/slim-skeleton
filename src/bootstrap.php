<?php

define('APP_DIR',     __DIR__.'/');
define('ROOT_DIR',    dirname(APP_DIR).'/');
define('RES_DIR',     ROOT_DIR.'res/');
define('STORAGE_DIR', ROOT_DIR.'storage/');
define('WWW_DIR',     ROOT_DIR.'public/');

use Slim\Container;

// Loading vendors
require __DIR__.'/../vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

$settings = [
    'settings' => require_once __DIR__.'/settings.php'
];

if (PHP_SAPI == 'cli') {
    $argv = $GLOBALS['argv'];
    array_shift($argv);

    $settings['environment'] = Slim\Http\Environment::mock([
        'REQUEST_URI' => '/'.implode('/', $argv)
    ]);
}

// Initialize Slim\App
$app = new Slim\App($settings);

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
