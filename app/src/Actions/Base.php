<?php
namespace App\Actions;

use Slim\Container;

abstract class Base
{
    /**
     * Slim DI Container
     *
     * @var \Slim\Container
     */
    private $container = null;

    public function __construct(Container $container = null)
    {
        if (null !== $container) {
            $this->container = $container;
        }

        $view = $this->container->get('view');
        $settings = $this->container->get('settings');

        $view->addData([
            '_title_'       => $settings['title'],
            '_description_' => $settings['description'],
        ]);
    }

    public function __get($var)
    {
        return $this->container->get($var);
    }
}
