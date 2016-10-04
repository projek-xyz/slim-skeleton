<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class CreateSchema extends Schema
{
    protected $constraints = [
        'bit' => ['bit'],
        'int' => ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'],
        'real' => ['real', 'double', 'float', 'decimal', 'numeric'],
        'date' => ['date', 'year', 'time', 'timestamp', 'datetime'],
        'char' => ['char', 'varchar', 'binary', 'varbinary'],
        'blob' => ['tinyblob', 'blob', 'mediumblob', 'longblob'],
        'text' => ['tinytext', 'text', 'mediumtext', 'longtext'],
        'enum' => ['enum'],
    ];

    protected $noValues = ['primary', 'index', 'unique', 'unsigned', 'auto_increment'];

    protected $indexes = [];

    /**
     *  {@inheritdoc}
     */
    public function build(Database $database = null)
    {
        $columns = [];

        foreach ($this->schema as $column => $definitions) {
            $columns[] = $column.' '.$this->buildDefinition($definitions);
            $this->params[] = $column;
        }

        return sprintf('CREATE TABLE %s (%s)', $this->table, implode(',', $columns));
    }

    protected function buildDefinition($definitions)
    {
        if (is_string($definitions)) {
            return $definitions;
        }

        $first = array_slice($definitions, 0, 1);
        $build = $this->getConstraint(key($first), reset($first));

        foreach (array_slice($definitions, 1) as $key => $value) {
            $build[] = $this->normalizeDefinition($key, $value);
        }

        return implode(' ', $build);
    }

    private function normalizeDefinition($key, $value)
    {
        if (is_bool($value)) {
            if ($key === 'null') {
                return $value === false ? 'NOT NULL' : ' NULL';
            }

            return $key;
        }

        if (is_numeric($key) && array_search($value, $this->noValues) !== false) {
            $key = $value == 'primary' ? 'primary key' : $value;
            $value = '';
        } else {
            $value = ' '.(null === $value ? 'null' : $value);
        }

        return strtoupper($key.$value);
    }

    private function getConstraint($definition, $value)
    {
        $column = [];
        $constraints = $this->flattenConstrains();

        if (is_numeric($definition) && isset($constraints[strtolower($value)])) {
            $column[] = strtoupper($value);
        } elseif (is_string($definition) && isset($constraints[strtolower($definition)])) {
            $column[] = strtoupper($definition);

            if (in_array($constraints[$definition], ['int', 'real', 'char'])) {
                $column[] = '('.$value.')';
            }
        }

        return [implode('', $column)];
    }

    private function flattenConstrains()
    {
        $constraints = [];

        foreach ($this->constraints as $type => $constrain) {
            foreach ($constrain as $datetype) {
                $constraints[$datetype] = $type;
            }
        }

        return $constraints;
    }
}
