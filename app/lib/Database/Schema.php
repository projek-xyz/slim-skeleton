<?php
namespace Projek\Slim\Database;

use Slim\PDO\Database as SlimDatabase;

abstract class Schema
{
    /**
     *  @var  string
     */
    protected $schema = '';

    /**
     *  @param  array $schema
     */
    public function __construct(array $schema)
    {
        if (method_exists($this, 'validate')) {
            $schema = $this->validate($schema);
        }

        $this->schema = $this->build($schema);
    }

    /**
     *  Build schema
     *
     *  @param  array $schema
     *  @param  SlimDatabase|null $database
     *  @return string
     */
    abstract protected function build(array $schema, SlimDatabase $database = null);

    /**
     *  @return string
     */
    public function __toString()
    {
        return $this->schema;
    }
}
