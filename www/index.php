<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(__DIR__.'/'.$uri)) {
    return false;
}

/** @define "APP_DIR" "../src/" */
$app = require dirname(__DIR__) . '/src/bootstrap.php';

// Setup routers
require_once APP_DIR.'routes/web.php';

// Go!
$app->run();
