<?php
namespace App\Providers;

use Slim\PDO\Database;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * Register this database provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['db'])) {
            throw new InvalidArgumentException('Database configuration not found');
        }

        $db = $settings['db'];

        if (!$db['dsn']) {
            $db['dsn'] = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $db['driver'],
                $db['host'],
                $db['name'],
                $db['charset']
            );
        }

        $container['db'] = new Database($db['dsn'], $db['user'], $db['pass']);
    }
}
