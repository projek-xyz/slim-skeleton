<?php

$app = require dirname(__DIR__).'/app/bootstrap.php';

// Setup dependencies
require_once APP_DIR.'dependencies.php';

// Setup middlewares
require_once APP_DIR.'middlewares.php';

// Setup routers
require_once APP_DIR.'routers.php';

// Go!
$app->run();
