<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database;

class Migration
{
    const TIMESTAMPS = 1;
    const SOFTDELETES = 2;

    /**
     * @var  Database
     */
    protected $database;

    /**
     * @var  string
     */
    protected $table;

    /**
     * @param  Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param  string $statement
     *
     * @return int|bool
     */
    public function exec($statement)
    {
        return $this->database->exec($statement);
    }

    /**
     * @param  string $query
     *
     * @return \PDOStatement
     */
    public function query($query)
    {
        return $this->database->query($query);
    }

    /**
     * @param  string $table
     * @param  array $schema
     *
     * @return bool
     */
    public function create($table, array $schema)
    {
        return $this->execSchema(new Schema\CreateSchema($table, $schema));
    }

    /**
     * @param  string $table
     * @param  array $schema
     *
     * @return bool
     */
    public function alter($table, array $schema)
    {
        return $this->execSchema(new Schema\AlterSchema($table, $schema));
    }

    /**
     * @param  string $table
     *
     * @return bool
     */
    public function delete($table)
    {
        return $this->execSchema(new Schema\DeleteSchema($table));
    }

    /**
     * @param  string $old
     * @param  string $new
     *
     * @return bool
     */
    public function rename($old, $new)
    {
        return $this->execSchema(new Schema\RenameSchema($old, $new));
    }

    /**
     * @param  Schema $schema
     *
     * @return bool
     */
    protected function execSchema(Schema $schema)
    {
        $query = $schema->build($this->database);

        return $this->database->exec($query);
    }
}
