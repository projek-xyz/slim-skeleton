<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Blueprint;
use Projek\Slim\Database\Models;

trait DefinitionsTrait
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

    protected $fields = [
        Blueprint::TIMESTAMPS  => [Models::CREATED, Models::UPDATED],
        Blueprint::SOFTDELETES => [Models::DELETED]
    ];

    protected $definitions = [
        Models::CREATED => ['datetime', 'null', 'default' => '0000-00-00 00:00:00'],
        Models::UPDATED => ['timestamp', 'null', 'on update' => 'current_timestamp'],
        Models::DELETED => ['datetime', 'null', 'default' => '0000-00-00 00:00:00'],
    ];

    private function buildDefinition(array $definitions)
    {
        if (empty($definitions)) {
            return '';
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
        if (is_numeric($key) && in_array($value, ['null', null])) {
            $key = $value;
            $value = true;
        }

        if (is_bool($value)) {
            if (in_array($key, ['null', null])) {
                return $value === false ? 'NOT NULL' : ' NULL';
            }

            return $key;
        }

        if (is_numeric($key) && array_search($value, $this->noValues) !== false) {
            $key = $value == 'primary' ? 'primary key' : $value;
            $value = '';
        } else {
            if ($value == 'current_timestamp') {
                $value = ' '.$value;
            } else {
                $value = ' \''.(null === $value ? 'null' : $value).'\'';
            }
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
