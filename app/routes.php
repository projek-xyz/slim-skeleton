<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

use App\Controllers\HomeControllers;

$app->get('/[{name}]', HomeControllers::class)->setName('home-page');
