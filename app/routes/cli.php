<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

$app->get('/', function () {
    return 'hallo'.PHP_EOL;
})->setName('home-page');

