<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/'.$uri)) {
    return false;
}

$appDir = dirname(__DIR__) . '/app/';
$app = new Slim\App(
    require $appDir.'bootstrap.php'
);

// Setup middlewares
require_once $appDir.'middlewares.php';

// Setup routers
require_once $appDir.'routes/web.php';

// Go!
$app->run();
