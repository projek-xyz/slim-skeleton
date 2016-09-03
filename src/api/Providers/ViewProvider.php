<?php
namespace App\Providers;

use Pimple\Container;
use Projek\Slim\View;
use Projek\Slim\Providers\ViewProvider as BaseProvider;

class ViewProvider extends BaseProvider
{
    /**
     * Register this plates view provider with a Pimple container
     *
     * @param Container|\Interop\Container\ContainerInterface $container
     */
    public function register(Container $container)
    {
        parent::register($container);

        /** @var View $view */
        $view = $container->get('view');

        $this->registerFolders([
            'error' => APP_DIR.'views/_errors',
            'layout' => APP_DIR.'views/_layouts',
            'partial' => APP_DIR.'views/_partials',
            'section' => APP_DIR.'views/_sections',
            'email' => APP_DIR.'views/_emails',
        ], $view);

        $view->addData($container->get('settings')['app']);
    }

    private function registerFolders(array $folders, View $view)
    {
        foreach ($folders as $name => $folder) {
            if (is_dir($folder)) {
                $view->addFolder($name, $folder);
            }
        }
    }
}
