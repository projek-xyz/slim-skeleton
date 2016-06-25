<?php
namespace App\Providers;

use Slim\PDO\Database;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use InvalidArgumentException;

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

        $db = $this->initialize($settings['db']);

        $container['db'] = new Database($db['dsn'], $db['user'], $db['pass']);
    }

    /**
     * Initialize database settings
     *
     * @param  array  $settings
     *
     * @return array
     */
    private function initialize(array $settings)
    {
        if (!$settings['dsn']) {
            $settings['dsn'] = sprintf(
                '%s:host=%s;dbname=%s;charset=%s',
                $settings['driver'],
                $settings['host'],
                $settings['name'],
                $settings['charset']
            );
        }

        return $settings;
    }
}
