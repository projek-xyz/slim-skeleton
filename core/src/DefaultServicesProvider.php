<?php
namespace Projek\Slim;

use Monolog\Logger as Monolog;
use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Collection;
use Slim\Http\Headers;
use Slim\PDO\Database as SlimDatabase;
use Valitron\Validator;

class DefaultServicesProvider implements ServiceProviderInterface
{
    /**
     * @param PimpleContainer|\Interop\Container\ContainerInterface $container
     */
    public function register(PimpleContainer $container)
    {
        /** @var Collection $settings */
        $settings = $container->get('settings');

        /**
         * Override default Slim foundHandler container.
         *
         * @return \Slim\Interfaces\InvocationStrategyInterface
         */
        $container['foundHandler'] = function () {
            return new Handlers\FoundHandler;
        };

        /**
         * Override default Slim errorHandler container.
         *
         * @return callable
         */
        $container['errorHandler'] = function () use ($settings) {
            return new Handlers\ErrorHandler($settings['displayErrorDetails']);
        };

        /**
         * Override default Slim notFoundHandler container.
         *
         * @return callable
         */
        $container['notFoundHandler'] = function () {
            return new Handlers\NotFoundHandler;
        };

        /**
         * Override default Slim Response
         *
         * @return \Psr\Http\Message\ResponseInterface
         */
        $container['response'] = function () use ($settings) {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Http\Response(200, $headers);

            return $response->withProtocolVersion($settings['httpVersion']);
        };

        /**
         * Setup default Console Commands
         *
         * @return array
         */
        $container[Console\Commands::class] = function () use ($settings) {
            $commands = [
                Console\Commands\MigrateCommand::class
            ];

            return array_merge($commands, $settings->get('commands', []));
        };

        /**
         * Setup Logger
         *
         * @return Logger
         */
        $container['logger'] = function () use ($settings) {
            if ($timezone = $settings['timezone']) {
                Monolog::setTimezone(new \DateTimeZone($timezone));
            }

            return new Logger($settings['basename'], $settings['logger'] ?: []);
        };

        /**
         * Setup Database
         *
         * @return SlimDatabase
         */
        $container['db'] = function () use ($settings) {
            $db = $this->initializeDatabase($settings['db']);

            return new SlimDatabase($db['dsn'], $db['user'], $db['pass'], [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_CLASS
            ]);
        };

        /**
         * Setup data model
         *
         * @param  \Slim\Container $container
         *
         * @return callable
         */
        $container['data'] = function ($container) {
            /**
             * Setup Validator callable
             *
             * @param  string $class
             *
             * @return Models|Object
             */
            return function ($class) use ($container) {
                if (!class_exists($class)) {
                    throw new \LogicException("Data model class {$class} not exists ");
                }

                $model = new \ReflectionClass($class);

                if (!$model->isSubclassOf(Database\Models::class)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Data model must be instance of %s, %s given',
                        Database\Models::class,
                        $model->getName()
                    ));
                }

                return $model->newInstance($container->get('db'));
            };
        };

        /**
         * Setup Migrator
         *
         * @param  \Slim\Container $container
         *
         * @return Database\Migrator
         */
        $container[Database\Migrator::class] = function ($container) use ($settings) {
            $directory = isset($settings['migration']['directory'])
                ? $settings['migration']['directory']
                : directory('app.data');

            return new Database\Migrator($container->get('db'), $directory);
        };

        /**
         * Setup View
         *
         * @return View
         */
        $container[View::class] = function () use ($settings) {
            $view = new View($settings['view']);

            $this->initializeViewFolders([
                'error' => '_errors',
                'layout' => '_layouts',
                'partial' => '_partials',
                'section' => '_sections',
                'email' => '_emails',
            ], $view);

            return $view;
        };

        /**
         * Setup Mail Driver
         *
         * @return Mailer\MailDriverInterface
         */
        $container[Mailer\MailDriverInterface::class] = function () use ($settings) {
            $driver = new Mailer\SmtpDriver($settings['mailer']);

            $driver->debugMode($settings['mode']);
            $driver->from($settings['app']['email'], $settings['app']['title']);

            return $driver;
        };

        /**
         * Setup Mailer
         *
         * @param  Container $container
         *
         * @return Mailer
         */
        $container['mailer'] = function ($container) {
            return new Mailer(
                $container->get(Mailer\MailDriverInterface::class)
            );
        };

        /**
         * Setup Validator
         *
         * @param  \Slim\Container $container
         *
         * @return callable
         */
        $container['validator'] = function ($container) {
            /**
             * Setup Validator callable
             *
             * @param  array|Collection|ServerRequestInterface $data
             * @param  array $rules
             *
             * @return Validator
             */
            return function ($data, array $rules) use ($container) {
                if ($data instanceof ServerRequestInterface) {
                    $data = $data->getParsedBody();
                }

                if ($data instanceof Collection) {
                    $data = $data->all();
                }

                if (!is_array($data)) {
                    throw new \InvalidArgumentException(
                        sprintf('First parameter should be an array %s given', gettype($data))
                    );
                }

                $validator = new Validator($data, [], $container->get('settings')['lang']['default']);

                $validator->rules($rules);

                return $validator;
            };
        };

        /**
         * Setup File System
         *
         * @return Filesystem
         */
        $container['filesystem'] = function () use ($settings) {
            return new FileSystem($settings->get('filesystem', []));
        };

        /**
         * Setup Uploader
         *
         * @return callable
         */
        $container['upload'] = function () use ($settings) {
            return new Uploader($settings['upload']);
        };

        require_once __DIR__ . '/helpers.php';
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
        if (!isset($settings['dsn']) || !$settings['dsn']) {
            $settings['charset'] = isset($settings['charset']) ? $settings['charset'] : 'utf8';
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
