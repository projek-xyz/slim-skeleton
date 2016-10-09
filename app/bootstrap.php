<?php

use Projek\Slim\Container;

/**
 * @codingStandardsIgnoreLine
 */
define('ROOT_DIR', dirname(__DIR__).DIRECTORY_SEPARATOR);

// Loading vendors
require ROOT_DIR.'vendor/autoload.php';

if (file_exists(ROOT_DIR.'.env')) {
    (new Dotenv\Dotenv(ROOT_DIR))->load();
}

$settings = require_once __DIR__.'/settings.php';

// Let's just use PHP Native sesion
if (!isset($_SESSION)) {
    session_name($settings['basename']);
    session_start();
}

return new Container(['settings' => $settings], ROOT_DIR);
