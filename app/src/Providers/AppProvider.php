<?php
namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Projek\Slim\Plates;

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
            $this->registerFolders([
                'error' => APP_DIR.'views/_errors',
                'layout' => APP_DIR.'views/_layouts',
                'partial' => APP_DIR.'views/_partials',
                'section' => APP_DIR.'views/_sections',
                'email' => APP_DIR.'views/_emails',
            ], $container['view']);
        }
    }

    private function registerFolders(array $folders, Plates $view)
    {
        foreach ($folders as $name => $folder) {
            if (is_dir($folder)) {
                $view->addFolder($name, $folder);
            }
        }
    }
}
