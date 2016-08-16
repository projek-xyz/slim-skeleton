<?php
namespace App\Controllers;

use App\ContainerAware;
use Slim\Container;

abstract class Controller
{
    use ContainerAware;

    /**
     * @param  Container $container
     */
    public function __construct(Container $container)
    {
        $settings = $container->get('settings');

        $this->container = $container;

        $this->view->addData($settings['app']);
    }
}
