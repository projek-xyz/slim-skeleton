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

    protected $migrationFile = '';

    public function setUp()
    {
        $this->settings = [
            'migration' => ['directory' => FIXTURES_DIR.'data'.DIRECTORY_SEPARATOR]
        ];

        parent::setUp();

        $this->db = $this->newMockDatabase();
        $this->migrator = $this->newMigrator();
        $this->migrationFile = $this->settings['migration']['directory'].'001-first-install.php';
    }

    public function tearDown()
    {
        $db = $this->container->get('db');
        $dummy = require $this->migrationFile;

        foreach ([$dummy['table'], Migrator::TABLE] as $table) {
            $db->exec('DROP TABLE IF EXISTS '.$table);
        }
    }

    public static function tearDownAfterClass()
    {
        try {
            // Recreate migrations table after tests are done.
            Container::instance()->get(Migrator::class)->migrate();
        } catch (\Exception $e) {
            // Do nothing.
        }
    }

    public function test_should_be_on_container()
    {
        $this->assertTrue($this->container->has(Migrator::class));
        $this->assertInstanceOf(Migrator::class, $this->container->get(Migrator::class));
    }

    public function test_should_migrate_inside_transaction()
    {
        $migrator = $this->newMigrator(
            $database = $this->newMockDatabase('beginTransaction', 'commit')
        );

        $database->expects($this->once())->method('beginTransaction');
        $database->expects($this->once())->method('commit');

        try {
            $migrator->migrate();
        } catch (\PDOException $e) {
            $this->markTestSkipped($e->getMessage());
        }
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

        if ($action == 'up') {
            $stmt = $db->insert(['migration' => $this->migrationFile, 'batch' => 1]);
            $mock = $mockDb->expects($this->once())->method('insert');
        } else {
            $stmt = $db->delete(Migrator::TABLE);
            $mock = $mockDb->expects($this->once())->method('delete');
        }

        $mock->will($this->returnValue($stmt));
        $migration->invoke($migrator, $this->migrationFile, 0, $action);
    }

    public function test_should_check_is_migrated()
    {
        $this->createMigrationTableIfNotExists();

        $db = $this->container->get('db');
        $migrator = $this->newMigrator(
            $mockDb = $this->newMockDatabase('select')
        );

        $migration = $this->makeMethodInvokable(Migrator::class, 'isMigrated');

        $stmt = $db->select(['migration']);
        $mockDb->expects($this->once())->method('select')->will($this->returnValue($stmt));

        $this->assertFalse($migration->invoke($migrator, $this->migrationFile));
    }

    public function actionProvider()
    {
        return [
            ['up'],
            ['down']
        ];
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
}
