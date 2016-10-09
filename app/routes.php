<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

$app->get('/[{name}]', App\Controllers\HomeController::class)->setName('home-page');
