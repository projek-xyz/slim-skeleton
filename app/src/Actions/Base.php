<?php
namespace App\Actions;

use Slim\Container;

/**
 * @property-read \Slim\PDO\Database db
 * @property-read \Slim\Flash\Messages flash
 * @property-read \Projek\Slim\Plates view
 * @property-read \Projek\Slim\Monolog log
 * @property-read \Projek\Slim\Flysystem fs
 * @property-read \Valitron\Validator validator
 * @property-read \App\Providers\NegotiatorProvider negotiator
 */
abstract class Base
{
    /**
     * Slim DI Container
     *
     * @var \Slim\Container
     */
    private $container = null;

    /**
     * @param \Slim\Container
     */
    public function __construct(Container $container = null)
    {
        if (null !== $container) {
            $this->container = $container;
        }

        $view = $this->container->get('view');
        $settings = $this->container->get('settings');

        $view->addData([
            '_title_' => $settings['title'],
            '_desc_'  => $settings['description'],
        ]);
    }

    /**
     * @return mixed
     */
    public function __get($var)
    {
        return $this->container->get($var);
    }
}
