<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

$app->get('/[{name}]', 'App\Actions\HomeAction:index')->setName('home-page');

$app->group('/test', function () {
    $this->get('/mail', 'App\Actions\HomeAction:email')->setName('email-test-age');
});

