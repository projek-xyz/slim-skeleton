<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database;

class Migration
{
    /**
     *  @var  Database
     */
    protected $database;

    /**
     *  @var  string
     */
    protected $table;

    /**
     *  @param  Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     *  @param  string $query
     */
    public function query($query)
    {
        return $this->database->query($query);
    }

    /**
     *  @param  string $table
     */
    public function create($table, array $schema)
    {
        $schema = new Schema\CreateSchema($schema);
        $stmt = $this->database->prepare((string) $schema);

        return $stmt->execute([$table]);
    }

    /**
     *  NOT IMPLEMENTED
     *
     *  @param  string $table
     */
    public function table($table, array $schema)
    {
        // $schema = new Schema\AlterSchema($schema);
        // $stmt = $this->database->prepare((string) $schema);

        // return $stmt->execute([$table]);
        return false;
    }

    /**
     *  @param  string $table
     */
    public function delete($table)
    {
        $stmt = $this->database->prepare('DROP TABLE ?');

        return $stmt->execute([$table]);
    }

    /**
     *  @param  string $old
     *  @param  string $new
     */
    public function rename($old, $new)
    {
        $stmt = $this->database->prepare('RENAME TABLE ? TO ?');

        return $stmt->execute([$old, $new]);
    }
}
