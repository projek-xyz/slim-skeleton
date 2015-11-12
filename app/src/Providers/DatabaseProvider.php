<?php
namespace App\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\PDO\Database;
use App\Models\Model;

class DatabaseProvider implements ServiceProviderInterface
{
    protected $settings = [];

    protected $db;

    public function register(Container $container)
    {
        $this->settings = $container->get('settings')['db'];

        $container['db'] = $this->getConnection();
    }

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
