<?php
namespace Projek\Slim;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Slim\Flash\Messages;
use Slim\PDO\Database;
use Valitron\Validator;

class DefaultServicesProvider implements ServiceProviderInterface
{
    /**
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(PimpleContainer $container)
    {
        $settings = $container->get('settings');

        require_once __DIR__.'/helpers.php';

        if ($settings['mode'] === 'production') {
            $container['errorHandler'] = function (PimpleContainer $container) use ($settings) {
                return $this->initializeHandlers(new Handlers\ErrorHandler($settings['displayErrorDetails']), $container);
            };
        }

        $container['notFoundHandler'] = function (PimpleContainer $container) {
            return $this->initializeHandlers(new Handlers\NotFoundHandler(), $container);
        };

        $container['logger'] = function () use ($settings) {
            return new Logger($settings['basename'], $settings['logger']);
        };

        $container['db'] = function () use ($settings) {
            $db = $this->initializeDatabase($settings['db']);
            $config = [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_CLASS
            ];

            return new Database($db['dsn'], $db['user'], $db['pass'], $config);
        };

        $container['data'] = function (PimpleContainer $container) {
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

        $container['view'] = function (PimpleContainer $container) {
            $engine = new View(
                $container->get('settings')['view'],
                $container->get('response')
            );

            $engine->loadExtension(
                new ViewExtension(
                    $container->get('router'),
                    $container->get('request')->getUri()
                )
            );

            $this->initializeViewFolders([
                'error' => '_errors',
                'layout' => '_layouts',
                'partial' => '_partials',
                'section' => '_sections',
                'email' => '_emails',
            ], $engine);

            $settings = $container->get('settings');

            if (isset($settings['app'])) {
                $engine->addData($settings['app']);
            }

            return $engine;
        };

        $container['mailer'] = function (PimpleContainer $container) {
            $settings = $container['settings'];

            $mailer = new Mailer($settings['mailer']);

            $mailer->setView($container['view']);
            $mailer->setLogger($container['logger']);

            $mailer->debugMode($settings['mode']);
            $mailer->setSender($settings['email'], $settings['name']);

            return $mailer;
        };

        /**
         * Setup Validator
         *
         * @param  PimpleContainer $container
         *
         * @return Validator
         */
        $container['validator'] = function (PimpleContainer $container) use ($settings) {
            return new Validator($container['request']->getParams(), [], $settings['lang']['default']);
        };
    }

    /**
     * @param  \Projek\Slim\Contracts\ViewableInterface $handlerClass
     * @param  Container  $container
     *
     * @return \Slim\Handlers\Error|\Slim\Handlers\NotFound|\Projek\Slim\Contracts\ViewableInterface
     */
    private function initializeHandlers($handlerClass, PimpleContainer $container)
    {
        $handlerClass->setView($container['view']);

        return $handlerClass;
    }

    /**
     * Initialize database settings
     *
     * @param  array  $settings
     *
     * @return array
     */
    private function initializeDatabase(array $settings)
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

    private function initializeViewFolders(array $folders, View $view)
    {
        foreach ($folders as $name => $folder) {
            if (is_dir($folder = $view->directory($folder))) {
                $view->addFolder($name, $folder);
            }
        }
    }
}
