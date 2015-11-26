<?php
namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\PDO\Database;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * Register this database provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        if (!isset($container->get('settings')['db'])) {
            throw new InvalidArgumentException('Database configuration not found');
        }

        $settings = $container->get('settings')['db'];

        if (!$settings['dsn']) {
            $settings['dsn'] = $settings['driver'].
                ':host='.$settings['host'].
                ';dbname='.$settings['name'].
                ';charset='.$settings['charset'];
        }

        $container['db'] = new Database($settings['dsn'], $settings['user'], $settings['pass']);
    }
}
