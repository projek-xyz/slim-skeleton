<?php
namespace App;

abstract class BaseAction
{
    private $container = null;

    public function __construct($container = null)
    {
        if (null !== $container) {
            $this->container = $container;
        }

        $view = $this->container->get('view');
        $settings = $this->container->get('settings');
        $view->addData(['_title_' => $settings['title']]);
    }

    public function __get($var)
    {
        return $this->container->get($var);
    }
}
