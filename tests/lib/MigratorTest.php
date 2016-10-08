<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Container;
use Projek\Slim\Database\Migrator;
use Slim\PDO\Database;

class MigratorTest extends DatabaseTestCase
{
    /**
     * @var  Migrator
     */
    protected $migrator;

    protected $db;

    public function setUp()
    {
        $this->settings = [
            'migration' => [
                'directory' => ROOT_DIR.'tests/stubs/',
            ]
        ];

        parent::setUp();

        $this->db = $this->newMockDatabase();
        $this->migrator = $this->newMigrator();
    }

    public function tearDown()
    {
        $this->container->get('db')->exec(
            sprintf('DROP TABLE %s', Migrator::TABLE)
        );
    }

    private function createMigrationTableIfNotExists()
    {
        $hasTable = $this->makeMethodInvokable(Migrator::class, 'hasMigrationTable');

        if (!$hasTable->invoke($this->migrator)) {
            $this->makeMethodInvokable(Migrator::class, 'createMigrationTable')
                ->invoke($this->migrator);
        }
    }

    private function newMigrator(Database $database = null)
    {
        if (null == $database) {
            $database = $this->container->get('db');
        }

        return new Migrator($database, $this->settings['migration']['directory']);
    }

    public static function tearDownAfterClass()
    {
        // Recreate migrations table after tests are done.
        Container::instance()->get(Migrator::class)->migrate();
    }

    public function test_should_be_on_container()
    {
        $this->assertTrue($this->container->has(Migrator::class));
        $this->assertInstanceOf(Migrator::class, $this->container->get(Migrator::class));
    }

    public function test_should_create_migration_table()
    {
        $hasTable = $this->makeMethodInvokable(Migrator::class, 'hasMigrationTable');

        if (!$hasMigrationTable = $hasTable->invoke($this->migrator)) {
            $this->assertFalse($hasMigrationTable);
            $createTable = $this->makeMethodInvokable(Migrator::class, 'createMigrationTable');
            $this->assertEquals(0, $createTable->invoke($this->migrator));
        }

        $this->assertTrue($hasTable->invoke($this->migrator));
    }

    /**
     * @dataProvider actionProvider
     */
    public function test_should_update_or_delete_from_migration_table($action)
    {
        $this->createMigrationTableIfNotExists();

        $db = $this->container->get('db');
        $migrator = $this->newMigrator(
            $mockDb = $this->newMockDatabase('insert', 'delete')
        );

        $migration = $this->makeMethodInvokable(Migrator::class, 'updateMigrationTable');
        $filename = $this->settings['migration']['directory'].'001-first-install.php';

        if ($action == 'up') {
            $stmt = $db->insert(['migration' => $filename, 'batch' => 1]);
            $method = $mockDb->expects($this->once())->method('insert');
        } else {
            $stmt = $db->delete(Migrator::TABLE);
            $method = $mockDb->expects($this->once())->method('delete');
        }

        $method->will($this->returnValue($stmt));

        $migration->invoke($migrator, $filename, 0, $action);
    }

    public function test_should_check_is_migrated()
    {
        $this->createMigrationTableIfNotExists();

        $db = $this->container->get('db');
        $migrator = $this->newMigrator(
            $mockDb = $this->newMockDatabase('select')
        );

        $migration = $this->makeMethodInvokable(Migrator::class, 'isMigrated');
        $filename = $this->settings['migration']['directory'].'001-first-install.php';

        $stmt = $db->select(['migration']);
        $mockDb->expects($this->once())->method('select')->will($this->returnValue($stmt));

        $this->assertFalse($migration->invoke($migrator, $filename));
    }

    public function test_should_migrate_inside_transaction()
    {
        $migrator = $this->newMigrator(
            $db = $this->newMockDatabase('beginTransaction', 'commit')
        );

        $db->expects($this->once())->method('beginTransaction');
        $db->expects($this->once())->method('commit');

        try {
            $migrator->migrate();
        } catch (\PDOException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function actionProvider()
    {
        return [
            ['up'],
            ['down']
        ];
    }
}
