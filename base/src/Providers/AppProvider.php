<?php
namespace Projek\Slim\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AppProvider implements ServiceProviderInterface
{
    /**
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(Container $container)
    {
        require __DIR__.'../helpers.php';
    }
}
