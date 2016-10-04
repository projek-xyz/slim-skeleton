<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class AlterSchema extends Schema
{
    /**
     *  {@inheritdoc}
     */
    protected function build(array $schema, Database $database = null)
    {
        return $schema;
    }

    /**
     *  {@inheritdoc}
     */
    public function __toString()
    {
        return 'ALTER TABLE ? '.parent::__toString();
    }
}
