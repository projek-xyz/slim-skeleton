<?php
namespace Projek\Slim\Database\Schema;

use Projek\Slim\Database\Schema;

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
    protected function build(array $schema)
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

        $constrains = [];
        $build = [];

        foreach ($this->constrains as $type => $constrain) {
            foreach ($constrain as $datetype) {
                $constrains[$datetype] = $type;
            }
        }

        foreach ($definitions as $definition => $value) {
            $column = '';

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

            if (true === $value) {
                $column .= ' '.$definition;
            } else {
                $column .= $definition.' '.$value;
            }

            $build[] = $column;
        }

        return implode(' ', $build);
    }

    /**
     *  {@inheritdoc}
     */
    public function __toString()
    {
        return 'CREATE TABLE ? '.parent::__toString();
    }
}
