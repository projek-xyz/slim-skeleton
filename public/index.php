<?php

if (PHP_SAPI == 'cli-server') {
    $file = $_SERVER['REQUEST_URI'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $exts = ['jpg', 'jpeg', 'gif', 'ico', 'js', 'css'];
    $ext  = pathinfo($path, PATHINFO_EXTENSION);

    if (is_file(__DIR__.$file) || in_array($ext, $exts)) return;
}

define('ROOT_DIR',  dirname(__DIR__).'/');
define('APP_DIR',   ROOT_DIR.'app/');
define('ASSET_DIR', ROOT_DIR.'asset/');

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

if (file_exists(APP_DIR.'.env')) {
    (new Dotenv\Dotenv(APP_DIR))->load();
}

// Initialize Slim\App
$app = new Slim\App([
    'settings' => require_once APP_DIR.'settings.php'
]);

$container = $app->getContainer();
$settings  = $container->get('settings');

// Let's set default timezone
if (isset($settings['timezone'])) {
    date_default_timezone_set($settings['timezone'] ?: 'UTC');
}

// Let's just use PHP Native sesion
if (!isset($_SESSION)) {
    session_name($settings['basename']);
    session_start();
}

// Setup dependencies
require_once APP_DIR.'dependencies.php';

// Setup middlewares
require_once APP_DIR.'middlewares.php';

// Setup routers
require_once APP_DIR.'routers.php';

// Go!
$app->run();
