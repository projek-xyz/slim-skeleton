<?php
namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AppProvider implements ServiceProviderInterface
{
    /**
     * Registering application error handler provider
     *
     * @param  Container  $container
     */
    public function register(Container $container)
    {
        if (isset($container['view'])) {
            $view = $container['view'];

            $view->addFolder('layouts', APP_DIR.'views/_layouts');
            $view->addFolder('error', APP_DIR.'views/errors');
            $view->addFolder('email', APP_DIR.'views/emails');
        }
    }
}
