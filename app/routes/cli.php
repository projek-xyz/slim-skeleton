<?php
/**
 * Application Routers
 *
 * @var \Slim\App $app
 */

use App\Commands\DefaultCommand;
use Projek\Slim\Commands\MigrateCommand;

$app->get('/', DefaultCommand::class)->setName('default-command');

$app->get('/migrate[/{action}]', MigrateCommand::class)->setName('migration-command');

