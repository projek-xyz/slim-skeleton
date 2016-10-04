<?php
namespace Projek\Slim\Database;

use Psr\Log\LogLevel;
use Slim\PDO\Database;

class Migrator
{
    const TABLE = 'migrations';

    /**
     *  @var  array
     */
    protected $migrations = [];

    protected $migration;

    /**
     *  @var  Database
     */
    protected $database;

    /**
     * @var  string
     */
    protected $directory;

    /**
     *  @param  Database $database
     *  @param  string $directory
     */
    public function __construct(Database $database, $directory = null)
    {
        $this->directory = $directory ?: directory('resources.data');
        if (!is_dir($this->directory)) {
            throw new \InvalidArgumentException('Migration directory not exists '.$this->directory);
        }

        foreach (glob($this->directory.'/*.{php,sql}', GLOB_BRACE ) as $migration) {
            $this->migrations[] = $migration;
        }

        $this->database = $database;
    }

    public function migrate($action = 'up')
    {
        $migrated = 0;

        try {
            $this->database->beginTransaction();

            if (!$this->hasMigrationTable()) {
                $this->createMigrationTable();
            }

            $batch = $this->getPriorMigration();
            $migrations = $action == 'down' ? $this->getPriorMigrationFiles($batch) : $this->migrations;

            foreach ($migrations as $filepath) {
                if ($this->isMigrated($filepath) && $action == 'up') {
                    continue;
                }

                $this->callMigration($filepath, $action);

                $this->updateMigrationTable($filepath, $batch, $action);

                ++$migrated;
            }

            $this->database->commit();

            return $migrated === 0 ? false : true;
        } catch (\PDOException $e) {
            $this->database->rollBack();

            logger(LogLevel::ERROR, $e->getMessage());

            return false;
        }
    }

    protected function migrateSql($filepath)
    {
        $content = file_get_contents($filepath);

        foreach (explode(';', $content) as $query) {
            $query = trim($query);
            if (empty($query)) {
                continue;
            }
            $this->database->query($query);
        }
    }

    protected function migratePhp($filepath, $action = 'up')
    {
        $callable = require $filepath;

        if (is_array($callable) && array_key_exists($action, $callable)) {
            $callable = $callable[$action];
        }

        if (is_callable($callable)) {
            $migration = $this->newMigration();

            if ($callable instanceof \Closure) {
                $callable->bindTo($migration);
            }

            call_user_func($callable, $migration);
        }
    }

    protected function callMigration($filepath, $action)
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);

        return call_user_func([$this, 'migrate'.ucfirst($ext)], $filepath, $action);
    }

    protected function isMigrated($filepath)
    {
        $batch = $this->database->select(['migration'])
            ->from(self::TABLE)
            ->where('migration', '=', basename($filepath))
            ->execute();

        return $batch->rowCount() > 0;
    }

    protected function createMigrationTable()
    {
        return $this->newMigration()->create(self::TABLE, [
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'migration' => ['varchar' => 255, 'unique' ,'null' => false],
            'batch' => ['int' => 11, 'null' => false]
        ]);
    }

    protected function hasMigrationTable()
    {
        $migration = $this->database->select(['count(*) count'])
            ->from('information_schema.tables')
            ->where('table_schema', '=', setting('db.name'))
            ->where('table_name', '=', self::TABLE)
            ->execute();

        return $migration->fetch()['count'] > 0;
    }

    protected function updateMigrationTable($filepath, $batch, $action)
    {
        if ($action == 'up') {
            $stmt = $this->database->delete(self::TABLE)
                ->where('batch', '=', $batch);
        } else {
            $stmt = $this->database->insert([
                'migration' => basename($filepath),
                'batch' => $batch
            ])->into(self::TABLE);
        }

        $stmt->execute();
    }

    protected function getPriorMigration()
    {
        $batch = $this->database->select(['batch'])
            ->from(self::TABLE)
            ->groupBy('batch')
            ->orderBy('batch', 'desc')
            ->execute();

        return $batch->rowCount() > 0 ? $batch->fetch()['batch'] + 1 : 1;
    }

    protected function getPriorMigrationFiles($batch)
    {
        $files = [];
        $migrations = $this->database->select(['migration'])
            ->from(self::TABLE)
            ->where('batch', '=', $batch)
            ->execute();

        if ($migrations->rowCount() == 0) {
            return $files;
        }

        foreach ($migrations->fetchAll() as $row) {
            $files[] = $this->directory.'/'.$row['migration'];
        }

        return $files;
    }

    protected function newMigration()
    {
        return new Migration($this->database);
    }
}
