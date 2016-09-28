<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

use App\Commands\DefaultCommand;

$app->get('/', DefaultCommand::class)->setName('default-command');

