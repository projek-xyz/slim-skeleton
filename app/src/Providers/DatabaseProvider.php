<?php
namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\PDO\Database;

class DatabaseProvider implements ServiceProviderInterface
{
    protected $settings = [];

    protected $db;

    /**
     * Register this database provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $this->settings = $container->get('settings')['db'];

        $container['db'] = $this->getConnection();
    }

    /**
     * Get database connection
     *
     * @param  string $name Connection name
     * @return App\Providers\DatabaseProvider
     */
    public function getConnection($name = 'default')
    {
        $conn = $this->settings[$name];

        if (!$conn['dsn']) {
            $conn['dsn'] = $conn['driver'].
                ':host='.$conn['host'].
                ';dbname='.$conn['name'].
                ';charset='.$conn['charset'];
        }

        $this->db = new Database($conn['dsn'], $conn['user'], $conn['pass']);

        return $this;
    }

    /**
     * Get database instance
     *
     * @return Slim\PDO\Database
     */
    public function getInstance()
    {
        return $this->db;
    }

    public function __call($method, $param)
    {
        if (is_callable([$this->db, $method])) {
            return call_user_func_array([$this->db, $method], $param);
        }
    }
}
