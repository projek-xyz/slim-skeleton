<?php
namespace Projek\Slim\Tests;

use Slim\PDO\Database;

class DatabaseTestCase extends TestCase
{
    public function setUp()
    {
        $this->settings = array_merge($this->settings, [
            'db' => [
                'driver' => getenv('DB_DRIVER') ?: 'mysql',
                'host'   => getenv('DB_HOST')   ?: 'localhost',
                'user'   => getenv('DB_USER')   ?: 'root',
                'pass'   => getenv('DB_PASS')   ?: '',
                'name'   => getenv('DB_NAME')   ?: '',
            ]
        ]);

        parent::setUp();
    }

    protected function newMockDatabase($methods = null)
    {
        $conf = $this->settings['db'];
        $dsn = sprintf('%s:host=%s;dbname=%s;charset=%s', $conf['driver'], $conf['host'], $conf['name'], 'utf8');
        $db = $this->getMockBuilder(Database::class)
            ->setConstructorArgs([$dsn, $conf['user'], $conf['pass']]);

        if ($methods) {
            $db->setMethods(is_array($methods) ? $methods : func_get_args());
        }

        return $db->getMock();
    }
}
