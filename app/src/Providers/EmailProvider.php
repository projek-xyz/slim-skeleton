<?php
/**
 * Created by PhpStorm.
 * User: feryardiant
 * Date: 26/06/2016
 * Time: 04.39
 */

namespace App\Providers;

use App\Mailer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EmailProvider implements ServiceProviderInterface
{
    /**
     * Registering application error handler provider
     *
     * @param  Container  $container
     */
    public function register(Container $container)
    {
        if (!isset($container['settings']['mailer'])) {
            throw new \InvalidArgumentException('Email configuration not found');
        }

        $container['mailer'] = function (Container $container) {
            $settings = $container['settings'];

            $mailer = new Mailer($settings['mailer']);

            $mailer->setView($container['view']);
            $mailer->setLogger($container['logger']);

            $mailer->debugMode($settings['mode']);
            $mailer->setSender($settings['email'], $settings['name']);

            return $mailer;
        };
    }
}
