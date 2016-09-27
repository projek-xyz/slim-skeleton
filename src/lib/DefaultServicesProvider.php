<?php
namespace Projek\Slim;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Projek\Slim\Handlers\FoundHandler;
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

        if ($settings['mode'] === 'production') {
            $container['errorHandler'] = function ($container) use ($settings) {
                return $this->initializeHandlers(
                    new Handlers\ErrorHandler($settings['displayErrorDetails']),
                    $container
                );
            };
        }

        $container['notFoundHandler'] = function ($container) {
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

        $container['data'] = function ($container) {
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
         * PSR-7 Response object
         *
         * @return \Psr\Http\Message\ResponseInterface
         */
        $container['response'] = function () use ($settings) {
            $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
            $response = new Response(200, $headers);

            return $response->withProtocolVersion($settings['httpVersion']);
        };

        /**
         * This service MUST return a SHARED instance
         * of \Slim\Interfaces\InvocationStrategyInterface.
         *
         * @return \Slim\Interfaces\InvocationStrategyInterface
         */
        $container['foundHandler'] = function () {
            return new FoundHandler;
        };

        $container['mailer'] = function ($container) {
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
        $container['validator'] = function ($container) use ($settings) {
            return new Validator($container['request']->getParams(), [], $settings['lang']['default']);
        };

        require_once __DIR__.'/helpers.php';
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
