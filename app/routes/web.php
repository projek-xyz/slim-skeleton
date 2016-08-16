<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

use App\Controllers\HomeController;

$app->get('/[{name}]', HomeController::class)->setName('home-page');

