<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class RenameSchema extends Schema
{
    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        $this->params = [$this->table, $this->schema];

        return sprintf('RENAME TABLE %s TO %s', $this->table, $this->schema);
    }
}
