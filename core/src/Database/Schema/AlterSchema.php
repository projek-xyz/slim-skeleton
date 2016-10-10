<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class AlterSchema extends Schema
{
    use DefinitionsTrait;

    protected $build;

    protected $callback;

    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        call_user_func($this->callback);

        return sprintf('ALTER TABLE %s %s', $this->table, (string) $this);
    }

    public function rename($newName)
    {
        $this->build[] = sprintf('RENAME TO %s', $newName);

        return $this;
    }

    public function addColumn($name, array $definition, $after = null)
    {
        return $this->column('add', [$name, $this->buildDefinition($definition), $after]);
    }

    public function modifyColumn($column, array $definition, $after = null)
    {
        return $this->column('modify', [$column, $this->buildDefinition($definition), $after]);
    }

    public function changeColumn($column, $newName, array $definition, $after = null)
    {
        return $this->column('change', [$column, $newName, $this->buildDefinition($definition), $after]);
    }

    public function dropColumn($column)
    {
        return $this->column('drop', [$column]);
    }

    protected function column($task, $params = [])
    {
        $count = count($params);
        array_unshift($params, strtoupper($task));

        if ($count > 1) {
            $after = array_pop($params);
            $params[] = $after ? 'AFTER '.$after : '';
        }

        $this->build[] = vsprintf('%s COLUMN'.str_repeat(' %s', $count), $params);

        return $this;
    }

    public function addIndex($indexName, array $columns)
    {
        $this->build[] = sprintf('ADD INDEX %s (%s)', $indexName, implode(',', $columns));

        return $this;
    }

    public function renameIndex($indexName, $newname)
    {
        $this->build[] = sprintf('RENAME INDEX %s TO %s', $indexName, $newname);

        return $this;
    }

    public function dropIndex($indexName)
    {
        $this->build[] = sprintf('DROP INDEX %s', $indexName);

        return $this;
    }

    public function addPrimary(array $columns)
    {
        $this->build[] = sprintf('ADD PRIMARY KEY (%s)', implode(',', $columns));

        return $this;
    }

    public function dropPrimary()
    {
        $this->build[] = 'DROP PRIMARY KEY';

        return $this;
    }

    public function addUnique($indexName, array $columns)
    {
        $this->build[] = sprintf('ADD UNIQUE INDEX %s (%s)', $indexName, implode(',', $columns));

        return $this;
    }

    public function addForeign($reference, $field, $column = null, $foreign = null)
    {
        $column = $column ?: $reference.'_'.$field;
        $foreign = $foreign ?: $this->table.'_'.$column;
        $format = 'ADD FOREIGN KEY %s (%s) REFERENCES %s (%s)';
        $this->build[] = sprintf($format, $foreign, $column, $reference, $field);

        return $this;
    }

    public function dropForeign($indexName)
    {
        $this->build[] = sprintf('DROP FOREIGN KEY %s', $indexName);

        return $this;
    }

    public function __toString()
    {
        return implode(', ', $this->build);
    }
}
