<?php
namespace App\Providers;

use Pimple\Container;
use Projek\Slim\View;
use Projek\Slim\Providers\ViewProvider as BaseProvider;

class ViewProvider extends BaseProvider
{
    /**
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(Container $container)
    {
        parent::register($container);

        /** @var View $view */
        $view = $container->get('view');

        $this->registerFolders([
            'error' => '_errors',
            'layout' => '_layouts',
            'partial' => '_partials',
            'section' => '_sections',
            'email' => '_emails',
        ], $view);

        $settings = $container->get('settings');

        if (isset($settings['app'])) {
            $view->addData($settings['app']);
        }
    }

    private function registerFolders(array $folders, View $view)
    {
        foreach ($folders as $name => $folder) {
            if (is_dir($folder = $view->directory($folder))) {
                $view->addFolder($name, $folder);
            }
        }
    }
}
