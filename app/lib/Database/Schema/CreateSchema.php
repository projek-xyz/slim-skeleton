<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class CreateSchema extends Schema
{
    use DefinitionsTrait;

    protected $indexes = [];

    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        $columns = [];
        $schema = $this->normalizeColumns($this->schema);

        foreach ($schema as $column => $definitions) {
            $columns[] = $column.' '.$this->buildDefinition($definitions);
            $this->params[] = $column;
        }

        return sprintf('CREATE TABLE `%s` (%s)', $this->table, implode(',', $columns));
    }

    protected function normalizeColumns($columns)
    {
        foreach ($columns as $column => $definition) {
            if (!is_numeric($column) && is_array($definition)) {
                continue;
            }

            foreach ($this->fields[$definition] as $field) {
                $columns[$field] = $this->definitions[$field];
            }

            unset($columns[$column]);
        }

        return $columns;
    }
}
