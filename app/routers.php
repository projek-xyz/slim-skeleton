<?php
/**
 * Application Routers
 */

$app->get('/', 'App\Actions\HomeAction:index')->setName('home-page');

