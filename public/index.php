<?php

if (PHP_SAPI == 'cli-server') {
    $file = $_SERVER['REQUEST_URI'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $exts = ['jpg', 'jpeg', 'gif', 'ico', 'js', 'css'];
    $ext  = pathinfo($path, PATHINFO_EXTENSION);

    if (is_file(__DIR__.$file) || in_array($ext, $exts)) return;
}

$app = require dirname(__DIR__).'/app/bootstrap.php';
$container = $app->getContainer();
$settings = $container->get('settings');

// Get detailed information while development
if ($settings['mode'] === 'development') {
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

// Setup new environment
// $env = $container->get('environment');
// $env->replace([
//     'REQUEST_SCHEME'       => 'https',
//     'HTTP_HOST'            => 'localhost',
//     'HTTP_ACCEPT_LANGUAGE' => 'id-ID,id;q=0.8,en-US;q=0.6',
// ]);

// Setup dependencies
require_once APP_DIR.'dependencies.php';

// Setup middlewares
require_once APP_DIR.'middlewares.php';

// Setup routers
require_once APP_DIR.'routers.php';

// Go!
$app->run();
