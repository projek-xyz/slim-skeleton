<?php
namespace Projek\Slim;

use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Projek\Slim\Handlers\FoundHandler;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Headers;
use Slim\PDO\Database;
use Valitron\Validator;

class DefaultServicesProvider implements ServiceProviderInterface
{
    /**
     * @param PimpleContainer|\Interop\Container\ContainerInterface $container
     */
    public function register(PimpleContainer $container)
    {
        $settings = $container->get('settings');

        /**
         * Override default Slim foundHandler container.
         *
         * @return \Slim\Interfaces\InvocationStrategyInterface
         */
        $container['foundHandler'] = function () {
            return new FoundHandler;
        };

        if ($settings['mode'] === 'production') {
            /**
             * Override default Slim errorHandler container.
             *
             * @return callable
             */
            $container['errorHandler'] = function () use ($settings) {
                return new Handlers\ErrorHandler($settings['displayErrorDetails']);
            };
        }

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
            $response = new Response(200, $headers);

            return $response->withProtocolVersion($settings['httpVersion']);
        };

        /**
         * Setup Logger
         *
         * @return Logger
         */
        $container['logger'] = function () use ($settings) {
            return new Logger($settings['basename'], $settings['logger']);
        };

        /**
         * Setup Database
         *
         * @return Database
         */
        $container['db'] = function () use ($settings) {
            $db = $this->initializeDatabase($settings['db']);
            $config = [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_CLASS
            ];

            return new Database($db['dsn'], $db['user'], $db['pass'], $config);
        };

        /**
         * Setup data model
         *
         * @return callable
         */
        $container['data'] = function ($container) {
            return function ($class) use ($container) {
                if (!class_exists($class)) {
                    throw new \LogicException("Data model class {$class} not exists ");
                }

                $model = new \ReflectionClass($class);

                if (!$model->isSubclassOf(Models::class)) {
                    throw new \InvalidArgumentException(
                        sprintf('Data model must be instance of %s, %s given', Models::class, $model->getName())
                    );
                }

                return $model->newInstance($container['db']);
            };
        };

        /**
         * Setup View
         *
         * @return View
         */
        $container['view'] = function () use ($settings) {
            $view = new View($settings['view']);

            $this->initializeViewFolders([
                'error' => '_errors',
                'layout' => '_layouts',
                'partial' => '_partials',
                'section' => '_sections',
                'email' => '_emails',
            ], $view);

            if (isset($settings['app'])) {
                $view->addData($settings['app']);
            }

            return $view;
        };

        /**
         * Setup Mailer
         *
         * @return Mailer
         */
        $container['mailer'] = function () use ($settings) {
            $mailer = new Mailer($settings['mailer']);

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
        $container['validator'] = function ($container) use ($settings) {
            return new Validator($container['request']->getParams(), [], $settings['lang']['default']);
        };

        /**
         * Setup File System
         *
         * @return Filesystem
         */
        $container['filesystem'] = function () use ($settings) {
            return new Filesystem(
                new Local(STORAGE_DIR, LOCK_EX, Local::DISALLOW_LINKS)
            );
        };

        /**
         * Setup Uploader
         *
         * @return callable
         */
        $container['upload'] = function () use ($settings) {
            return new Uploader($settings['upload']);
        };

        if (class_exists(Client::class)) {
            /**
             * Setup HTTP Client
             *
             * @return Client
             */
            $container['httpClient'] = function () {
                return new Client();
            };
        }

        require_once __DIR__.'/helpers.php';
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
