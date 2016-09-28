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
         * This service MUST return a SHARED instance
         * of \Slim\Interfaces\InvocationStrategyInterface.
         *
         * @return \Slim\Interfaces\InvocationStrategyInterface
         */
        $container['foundHandler'] = function () {
            return new FoundHandler;
        };

        if ($settings['mode'] === 'production') {
            $container['errorHandler'] = function () use ($settings) {
                return new Handlers\ErrorHandler($settings['displayErrorDetails']);
            };
        }

        $container['notFoundHandler'] = function () {
            return new Handlers\NotFoundHandler;
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
                    throw new \InvalidArgumentException(
                        sprintf('Data model must be instance of %s, %s given', Models::class, $model->getName())
                    );
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

        $container['filesystem'] = function () use ($settings) {
            return new Filesystem(
                new Local(STORAGE_DIR, LOCK_EX, Local::DISALLOW_LINKS)
            );
        };

        $container['upload'] = function () use ($settings) {
            return function (UploadedFileInterface $file) use ($settings) {
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    return false;
                }

                if (!in_array($file->getClientMediaType(), $settings['upload']['extensions'])) {
                    throw new \InvalidArgumentException('Filetype not allowed');
                }

                $file->moveTo($settings['upload']['directory'].'/'.$file->getClientFilename());
            };
        };

        $container['httpClient'] = function () {
            return new Client();
        };

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
