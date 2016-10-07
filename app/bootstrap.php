<?php

/** @define "ROOT_DIR" "../" */
define('ROOT_DIR', dirname(__DIR__).'/');

use Projek\Slim\Container;

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

$bootstrap = [
    'settings' => require_once __DIR__.'/settings.php'
];

$settings =& $bootstrap['settings'];

if (PHP_SAPI == 'cli') {
    $argv = $GLOBALS['argv'];
    array_shift($argv);

    $bootstrap['environment'] = Slim\Http\Environment::mock([
        'REQUEST_URI' => '/'.implode('/', $argv)
    ]);
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

return new Container($bootstrap, ROOT_DIR);
