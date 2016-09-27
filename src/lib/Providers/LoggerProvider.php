<?php
namespace Projek\Slim\Providers;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Projek\Slim\Logger;

class LoggerProvider implements ServiceProviderInterface
{
    /**
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['logger'])) {
            throw new InvalidArgumentException('Logger configuration not found');
        }

        $basename = isset($settings['basename']) ? $settings['basename'] : 'slim-config';
        $settings['logger']['timezone'] = $settings['timezone'];

        $container['logger'] = new Logger($basename, $settings['logger']);
    }
}
