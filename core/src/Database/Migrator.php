<?php
namespace Projek\Slim\Database;

use Projek\Slim\Console\Output;
use Slim\PDO\Database;

class Migrator
{
    const TABLE = 'migrations';

    /**
     *  @var  array
     */
    protected $migrations = [];

    /**
     * @var Blueprint
     */
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
     * @var  Output
     */
    protected $output;

    /**
     *  @param  Database $database
     *  @param  string $directory
     */
    public function __construct(Database $database, $directory = null)
    {
        $this->directory = $directory ?: directory('app.data');
        if (!is_dir($this->directory)) {
            throw new \InvalidArgumentException('Migration directory not exists '.$this->directory);
        }

        foreach (glob($this->directory.'*.{php,sql}', GLOB_BRACE) as $migration) {
            $this->migrations[] = $migration;
        }

        $this->database = $database;
    }

    public function setOutput(Output $output)
    {
        $this->output = $output;

        return $this;
    }

    public function migrate($action = 'up')
    {
        $migrated = 0;

        try {
            $this->database->beginTransaction();

            if (!$this->hasMigrationTable()) {
                $this->createMigrationTable();
            }

            $isDown = $action == 'down';
            $batch = $this->getPriorMigration();
            $migrations = $isDown ? $this->getPriorMigrationFiles($batch) : $this->migrations;
            $files = count($migrations);

            if ($migrations) {
                $this->out('<green>Migration start:</green>');
            }

            foreach ($migrations as $filepath) {
                if (($this->isMigrated($filepath) && !$isDown) ||
                    (!$this->isMigrated($filepath) && $isDown)) {
                    continue;
                }

                $this->callMigration($filepath, $action);

                $this->updateMigrationTable($filepath, $batch, $action);

                $this->out(
                    sprintf('<yellow>%s</yellow> %s', $isDown ? 'Reseted: ' : 'Migrated:', basename($filepath)),
                    true
                );

                ++$migrated;
            }

            $this->database->commit();

            $this->out(
                $migrated > 0 ? '<green>Done successfully migrated</green> '.$files.' <green>file(s)</green>' : sprintf(
                    '<yellow>No migration executed</yellow>%s',
                    $files > 0 ? ' '.$files.' <yellow>file(s) already migrated</yellow>' : ''
                ),
                true
            );

            return true;
        } catch (\PDOException $e) {
            $this->database->rollBack();

            throw $e;
        }
    }

    protected function out($message, $tab = false)
    {
        if ($this->output) {
            if ($tab) {
                $this->output->tab();
            }

            $this->output->out($message);
        }
    }

    protected function callMigration($filepath, $action)
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);

        call_user_func([$this, 'migrate'.ucfirst($ext)], $filepath, $action);
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
        $migration = require $filepath;
        $callable = null;
        $schema = $this->newBlueprint();

        if (is_array($migration)) {
            if (array_key_exists($action, $migration)) {
                $callable = $migration[$action];
            }

            if (array_key_exists('table', $migration)) {
                $schema->table($migration['table']);
            }

            if (is_array($migration['up']) || !array_key_exists('down', $migration)) {
                $callable = function (Blueprint $schema) use ($migration, $action) {
                    if ($action == 'up') {
                        $schema->create($migration[$action]);
                    } else {
                        $schema->delete();
                    }
                };
            }
        }

        if (is_callable($callable)) {
            if ($callable instanceof \Closure) {
                $callable = $callable->bindTo($this->database);
            }
            $callable($schema);
        } else {
            throw new \RuntimeException('No migration callable found in '.$filepath);
        }
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
        return $this->newBlueprint(self::TABLE)->create([
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'migration' => ['varchar' => 255, 'unique' ,'null' => false],
            'batch' => ['int' => 11, 'null' => false]
        ]);
    }

    protected function hasMigrationTable()
    {
        $migration = $this->database->select(['count(*) count'])
            ->from('information_schema.tables')
            ->where('table_schema', '=', config('db.name'))
            ->where('table_name', '=', self::TABLE)
            ->execute();

        return $migration->fetch()['count'] > 0;
    }

    protected function updateMigrationTable($filepath, $batch, $action)
    {
        $filename = basename($filepath);

        if ($action == 'up') {
            $stmt = $this->database->insert([
                'migration' => $filename,
                'batch' => $batch + 1
            ])->into(self::TABLE);
        } else {
            $stmt = $this->database->delete(self::TABLE)
                ->where('migration', '=', $filename)
                ->where('batch', '=', $batch);
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

        return $batch->rowCount() > 0 ? $batch->fetch()['batch'] : 0;
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
            $files[] = $this->directory.$row['migration'];
        }

        return array_reverse($files);
    }

    private function newBlueprint($table = null)
    {
        return new Blueprint($this->database, $table);
    }
}
