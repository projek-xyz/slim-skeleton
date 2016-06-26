<?php
namespace App;

use Slim\Container;

abstract class Actions
{
    use Utils\ContainerAware;
    /**
     * @var \Slim\Container
     */
    private $container = null;

    /**
     * @param \Slim\Container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $settings = $this->container->get('settings');

        $this->view->addData([
            '_title_' => $settings['title'],
            '_desc_'  => $settings['description'],
        ]);
    }
}
