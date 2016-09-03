<?php
namespace Projek\Slim\Providers;

use Projek\Slim\Models;
use Slim\PDO\Database;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * Register this data provider with a Pimple container
     *
     * @param Container $container
     */
    public function register(Container $container)
    {
        if (!isset($container['settings']['db'])) {
            throw new \InvalidArgumentException('Database configuration not found');
        }

        $container['db'] = function (Container $container) {
            $db = $this->initializeDbSettings($container['settings']['db']);

            return new Database($db['dsn'], $db['user'], $db['pass']);
        };

        $container['data'] = function (Container $container) {
            $db = $container['db'];

            return function ($class) use ($db) {
                if (!class_exists($class)) {
                    throw new \LogicException("Data model class {$class} not exists ");
                }

                $model = new \ReflectionClass($class);

                if (!$model->isSubclassOf(Models::class)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Data model must be instance of %s, %s given',
                        Models::class,
                        $model->getName()
                    ));
                }

                return $model->newInstance($db);
            };
        };
    }

    /**
     * Initialize database settings
     *
     * @param  array  $settings
     *
     * @return array
     */
    private function initializeDbSettings(array $settings)
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
