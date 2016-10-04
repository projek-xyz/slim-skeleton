<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class AlterSchema extends Schema
{
    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        return 'ALTER TABLE '.$this->table.' ';
    }
}
