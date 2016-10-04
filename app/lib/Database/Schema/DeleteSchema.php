<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class DeleteSchema extends Schema
{
    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        return sprintf('DROP TABLE %s', $this->table);
    }
}
