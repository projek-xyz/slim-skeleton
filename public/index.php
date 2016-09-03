<?php

/** @define "APP_DIR" "../src/" */
$app = require dirname(__DIR__) . '/src/bootstrap.php';

// Setup dependencies
require_once APP_DIR.'dependencies.php';

// Setup middlewares
require_once APP_DIR.'middlewares.php';

// Setup routers
require_once APP_DIR.'routes/web.php';

// Go!
$app->run();
