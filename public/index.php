<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/'.$uri)) {
    return false;
}

/** @var "../app/" $app_dir */
$app = require dirname(__DIR__) . '/app/bootstrap.php';

// Setup middlewares
require_once directory('app').'middlewares.php';

// Setup routers
require_once directory('app').'routes/web.php';

// Go!
$app->run();
