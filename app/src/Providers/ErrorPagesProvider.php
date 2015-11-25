<?php
namespace App\Providers;

use App\Handlers;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ErrorPagesProvider implements ServiceProviderInterface
{
    /**
     * Register Error and Not found page handler with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        // Get detailed information while development
        if ($settings['mode'] === 'development') {
            $settings['displayErrorDetails'] = true;
        }

        $container['errorHandler'] = function ($c) use ($settings) {
            return new Handlers\ErrorHandler($settings['displayErrorDetails']);
        };

        $container['notFoundHandler'] = function ($c) {
            return new Handlers\NotFoundHandler;
        };
    }
}
