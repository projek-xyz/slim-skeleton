<?php
namespace App\Providers;

use Base\Handlers;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ErrorHandlersProvider implements ServiceProviderInterface
{
    /**
     * Registering application error handler provider
     *
     * @param  Container  $container
     */
    public function register(Container $container)
    {
        $settings = $container['settings'];

        if ($settings['mode'] === 'production') {
            $container['errorHandler'] = function (Container $container) use ($settings) {
                return $this->initHandler(new Handlers\ErrorHandler($settings['displayErrorDetails']), $container);
            };
        }

        $container['notFoundHandler'] = function (Container $container) {
            return $this->initHandler(new Handlers\NotFoundHandler(), $container);
        };
    }

    /**
     * @param  \App\Contracts\ViewableInterface|\App\Contracts\LoggableInterface  $handlerClass
     * @param  Container  $container
     *
     * @return \Slim\Handlers\Error|\Slim\Handlers\NotFound
     */
    private function initHandler($handlerClass, Container $container)
    {
        $handlerClass->setView($container['view']);
        $handlerClass->setLogger($container['logger']);

        return $handlerClass;
    }
}
