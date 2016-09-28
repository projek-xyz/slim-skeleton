<?php

namespace Projek\Slim;

/**
 * @property-read \Slim\PDO\Database db
 * @property-read \Slim\Collection settings
 * @property-read \Valitron\Validator validator
 * @property-read Mailer mailer
 * @property-read Logger logger
 * @property-read View view
 * @property-read callable data
 * @property-read callable upload
 * @method Models data(string $modelClass)
 */
abstract class Action
{
    /**
     * Slim\Container instance
     *
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get \Slim\Container name
     *
     * @param  string $name Container Name
     *
     * @return mixed
     * @throws \Slim\Exception\ContainerValueNotFoundException
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Call \Slim\Container callable name
     *
     * @param  string $method Container Name
     * @param  array  $params Parameters
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $params)
    {
        if ($this->container->has($method)) {
            $obj = $this->container->get($method);
            if (is_callable($obj)) {
                return call_user_func_array($obj, $params);
            }
        }

        throw new \BadMethodCallException("Method $method is not a valid method");
    }
}
