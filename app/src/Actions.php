<?php
namespace App;

use Slim\Container;

abstract class Actions
{
    use ContainerAware;

    /**
     * @param  Container $container
     */
    public function __construct(Container $container)
    {
        $settings = $container->get('settings');

        $this->container = $container;

        $this->view->addData([
            '_title_' => $settings['title'],
            '_desc_'  => $settings['description'],
        ]);
    }
}
