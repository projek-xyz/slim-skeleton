<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;
use Slim\PDO\Database;

class CreateSchema extends Schema
{
    protected $constrains = [
        'bit' => ['bit'],
        'int' => ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'],
        'real' => ['real', 'double', 'float', 'decimal', 'numeric'],
        'date' => ['date', 'year', 'time', 'timestamp', 'datetime'],
        'char' => ['char', 'varchar', 'binary', 'varbinary'],
        'blob' => ['tinyblob', 'blob', 'mediumblob', 'longblob'],
        'text' => ['tinytext', 'text', 'mediumtext', 'longtext'],
        'enum' => ['enum'],
    ];

    /**
     *  {@inheritdoc}
     */
    protected function build(array $schema, Database $database = null)
    {
        $columns = [];

        foreach ($schema as $column => $def) {
            $columns[] = $column.' '.$this->buildDefinition($def);
        }

        return '('.implode(',', $columns).')';
    }

    protected function buildDefinition($definitions)
    {
        if (is_string($definitions)) {
            return $definitions;
        }

        $build = [];

        foreach ($definitions as $definition => $value) {
            $column = $this->getColumnConstrain($definition, $value);

            if (is_bool($value)) {
                if ($definition === 'null') {
                    $column .= $value === false ? ' NOT NULL' : ' NULL';
                } else {
                    $column .= ' '.$definition;
                }
            } else {
                $column .= $definition.' '.$value;
            }

            $build[] = $column;
        }

        return implode(' ', $build);
    }

    private function getColumnConstrain($definition, $value)
    {
        $column = '';
        $constrains = $this->flattenConstrains();

        foreach ($constrains as $constrain) {
            if (!isset($definition[$constrain])) {
                throw new \InvalidArgumentException('No column constraint defined');
            } else {
                $column = $constrain;
                if (in_array($constrains[$constrain], ['int', 'real', 'char'])) {
                    $column .= '('.$value.')';
                }
            }
        }

        return $column;
    }

    private function flattenConstrains()
    {
        $constrains = [];

        foreach ($this->constrains as $type => $constrain) {
            foreach ($constrain as $datetype) {
                $constrains[$datetype] = $type;
            }
        }

        return $constrain;
    }

    /**
     *  {@inheritdoc}
     */
    public function __toString()
    {
        return 'CREATE TABLE ? '.parent::__toString();
    }
}
