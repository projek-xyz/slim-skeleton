<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database;

class Migrator
{
    /**
     *  @var  array
     */
    protected $migrations = [];

    /**
     *  @var  Database
     */
    protected $database

    /**
     *  @param  string $directory
     */
    public function __construct($directory, Database $database)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Migration directory not exists');
        }

        foreach (glob($directory.'/*.{php,sql}', GLOB_BRACE ) as $migration) {
            $this->migrations[] = $migration;
        }

        $this->database = $database;
    }

    public function migrate($do = 'up')
    {
        foreach ($this->migrations as $migration) {
            $ext = pathinfo($migration, PATHINFO_EXTENSION);

            call_user_func([$this, 'migrate'.ucfirst($ext)], $migration, $do);
        }
    }

    protected function migrateSql($filepath, $do = 'up')
    {
        $content = file_get_contents($filepath);

        foreach (explode(';', $content) as $query) {
            $this->database->query($query)->execute();
        }
    }

    protected function migratePhp($filepath, $do = 'up')
    {
        $migration = require $filepath;

        if (is_array($migration) && array_key_exists($do, $migration)) {
            $migration = $migration[$do];
        }

        if (is_callable($migration)) {
            $migrator = new Migration($this->database);

            if ($migration instanceof \Closure) {
                $migration->bindTo($migrator);
            }

            call_user_func($migration, $migrator);
        }
    }
}
