<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;

class AlterSchema extends Schema
{
    /**
     *  {@inheritdoc}
     */
    protected function build(array $schema)
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
