<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database as SlimDatabase;

abstract class Schema
{
    /**
     *  @var  string
     */
    protected $table = '';

    /**
     *  @var  mixed
     */
    protected $schema = '';

    /**
     *  @var  array
     */
    protected $params = [];

    /**
     *  @param  string $table
     *  @param  array|string $schema
     */
    public function __construct($table, $schema = null)
    {
        $this->table = $table;
        $this->schema = $schema;
    }

    /**
     *  Build schema
     *
     *  @param  SlimDatabase|null $database
     *  @return string
     */
    abstract public function build(SlimDatabase $database = null);

    /**
     * @return  array
     */
    public function params()
    {
        return $this->params;
    }
}
