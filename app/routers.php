<?php
/**
 * Application Routers
 */

$app->get('/[{name}]', 'App\Actions\HomeAction:index')->setName('home-page');

