<?php
namespace Projek\Slim\Providers;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Projek\Slim\View;
use Projek\Slim\ViewExtension;

class ViewProvider implements ServiceProviderInterface
{
    /**
     * Register this plates view provider with a Pimple container
     *
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(Container $container)
    {
        if (!isset($container->get('settings')['view'])) {
            throw new InvalidArgumentException('Template configuration not found');
        }

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

        $container['view'] = $engine;
    }
}
