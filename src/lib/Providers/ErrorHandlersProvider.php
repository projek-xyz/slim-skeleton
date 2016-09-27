<?php
namespace Projek\Slim\Providers;

use Projek\Slim\Handlers;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ErrorHandlersProvider implements ServiceProviderInterface
{
    /**
     * @param Container|\Interop\Container\ContainerInterface $container
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
     * @param  \Projek\Slim\Contracts\ViewableInterface $handlerClass
     * @param  Container  $container
     *
     * @return \Slim\Handlers\Error|\Slim\Handlers\NotFound|\Projek\Slim\Contracts\ViewableInterface
     */
    private function initHandler($handlerClass, Container $container)
    {
        $handlerClass->setView($container['view']);

        return $handlerClass;
    }
}
