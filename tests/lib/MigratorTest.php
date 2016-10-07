<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Database\Migrator;

class MigratorTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'db' => [
                'driver' => getenv('DB_DRIVER'),
                'host'   => getenv('DB_HOST'),
                'user'   => getenv('DB_USER'),
                'pass'   => getenv('DB_PASS'),
                'name'   => getenv('DB_NAME'),
            ],
            'migration' => [
                'directory' => ROOT_DIR.'tests/stubs',
            ]
        ];

        parent::setUp();
    }

    public function test_should_be_true()
    {
        $this->assertTrue($this->container->has('migrator'));
        $this->assertInstanceOf(Migrator::class, $this->container->get('migrator'));
    }

    public function test_should_create_migration_table()
    {
        $migrator = $this->container->get('migrator');
        $hasTable = $this->makeMethodInvokable(Migrator::class, 'hasMigrationTable');

        if (!$hasMigrationTable = $hasTable->invoke($migrator)) {
            $this->assertFalse($hasMigrationTable);
            $createTable = $this->makeMethodInvokable(Migrator::class, 'createMigrationTable');
            $this->assertTrue(\PDOStatement::class, $createTable->invoke($migrator));
        }

        $this->assertTrue($hasTable->invoke($migrator));
    }
}
