<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database;

/**
 * @method bool rename($newName)
 */
class Blueprint
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
     * @param  string $table
     */
    public function __construct(Database $database, $table = null)
    {
        $this->database = $database;
        $this->table = $table;
    }

    /**
     * @param  string $table
     *
     * @return static
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param  array $schema
     *
     * @return bool
     */
    public function create(array $schema)
    {
        return $this->execSchema(new Schema\CreateSchema($this->table, $schema));
    }

    /**
     * @param  \Closure $callable
     *
     * @return bool
     */
    public function alter(\Closure $callable)
    {
        $schema = new Schema\AlterSchema($this->table);
        $callable = $callable->bindTo($schema);

        $callable($this->database);

        return $this->execSchema($schema);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->execSchema(new Schema\DeleteSchema($this->table));
    }

    /**
     * @param  string $method
     * @param  array $args
     *
     * @return bool
     */
    public function __call($method, $args)
    {
        $schema = new Schema\AlterSchema($this->table);

        if (is_callable([$schema, $method])) {
            return $this->execSchema(call_user_func_array([$schema, $method], $args));
        }

        throw new \BadMethodCallException("Method $method is not a valid method");
    }

    /**
     * @param  Schema $schema
     *
     * @return bool
     */
    protected function execSchema(Schema $schema)
    {
        $query = $schema->build($this->database);

        return $this->database->exec(trim($query));
    }
}
