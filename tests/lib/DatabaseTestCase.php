<?php
namespace Projek\Slim\Tests;

use Slim\PDO\Database;

class DatabaseTestCase extends TestCase
{
    public function setUp()
    {
        $this->settings = array_merge($this->settings, [
            'db' => [
                'driver' => getenv('DB_DRIVER'),
                'host'   => getenv('DB_HOST'),
                'user'   => getenv('DB_USER'),
                'pass'   => getenv('DB_PASS'),
                'name'   => getenv('DB_NAME'),
            ]
        ]);

        parent::setUp();
    }
    protected function newMockDatabase($methods = null)
    {
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $this->settings['db']['driver'],
            $this->settings['db']['host'],
            $this->settings['db']['name'],
            'utf8'
        );

        $db = $this->getMockBuilder(Database::class)
            ->setConstructorArgs([$dsn, $this->settings['db']['user'], $this->settings['db']['pass']]);

        if ($methods) {
            $db->setMethods(is_array($methods) ? $methods : func_get_args());
        }

        return $db->getMock();
    }
}
