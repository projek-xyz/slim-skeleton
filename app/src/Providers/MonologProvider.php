<?php
namespace App\Providers;

use App\Libraries;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use InvalidArgumentException;

class MonologProvider implements ServiceProviderInterface
{
    /**
     * Register this monolog provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        if (!isset($container->get('settings')['logger'])) {
            throw new InvalidArgumentException('Logger configuration not found');
        }

        $container['logger'] = new Libraries\Monolog('slim-app', $container->get('settings')['logger']);
    }
}
