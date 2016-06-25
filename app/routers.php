<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

$app->get('/[{name}]', 'App\Actions\HomeAction:index')->setName('home-page');

