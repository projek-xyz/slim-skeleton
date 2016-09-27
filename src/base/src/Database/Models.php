<?php
namespace Projek\Slim\Database;

use Projek\Slim\Contracts\ModelInterface;
use Slim\PDO\Database;
use Slim\PDO\Statement\StatementContainer;

abstract class Models implements ModelInterface
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var string
     */
    protected $primary = 'id';

    /**
     * @var bool
     */
    protected $timestamps = true;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param Database|array|null $props
     */
    public function __construct($props = null)
    {
        if ($props instanceof Database) {
            $this->setDatabase($props);
        } elseif (is_array($props)) {
            $this->setAttributes($props);
        }
    }

    /**
     * @inheritdoc
     */
    public function create(array $pairs)
    {
        if (!$this->table) {
            return false;
        }

        if ($this->timestamps) {
            $pairs[ModelInterface::CREATED] = $pairs[ModelInterface::UPDATED] = $this->freshDate();
        }

        $query = $this->db->insert(array_keys($pairs))
            ->into($this->table)
            ->values(array_values($pairs));

        return (int) $query->execute(true);
    }

    /**
     * @inheritdoc
     */
    public function insert(array $data)
    {
        if (!$this->table) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            foreach ($data as $i => $entry) {
                $this->db->insert(array_keys($entry))
                    ->into($this->table)
                    ->values(array_values($entry))
                    ->execute();
            }

            $this->db->commit();
        } catch (\PDOException $e) {
            $this->db->rollBack();

            throw $e;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function get(array $columns = [], $terms = null)
    {
        if (!$this->table) {
            return false;
        }

        $query = $this->db->select($columns)->from($this->table);

        $this->normalizeTerms($query, $terms);

        return $query->execute();
    }

    /**
     * @inheritdoc
     */
    public function find($terms = null)
    {
        return $this->get([], $terms)->fetch();
    }

    /**
     * @inheritdoc
     */
    public function update(array $pairs, $terms = null)
    {
        if (!$this->table) {
            return false;
        }

        if ($this->timestamps) {
            $pairs[ModelInterface::UPDATED] = $this->freshDate();
        }

        $query = $this->db->update(array_filter($pairs))->table($this->table);

        $this->normalizeTerms($query, $terms);

        return $query->execute();
    }

    /**
     * @inheritdoc
     */
    public function delete($terms)
    {
        if (method_exists($this, 'softDelete')) {
            return $this->softDelete($terms);
        }

        if (!$this->table) {
            return false;
        }

        $query = $this->db->delete($this->table);

        $this->normalizeTerms($query, $terms);

        return $query->execute();
    }

    /**
     * Count all data
     *
     * @param  callable|array|int  $terms
     * @return int
     */
    public function count($terms = null)
    {
        if (!$this->table) {
            return 0;
        }

        $query = $this->db->select(['count(*) count'])
            ->from($this->table);

        $this->normalizeTerms($query, $terms);

        return (int) $query->execute()->fetch()['count'];
    }

    /**
     * @inheritdoc
     */
    public function primary()
    {
        return $this->primary;
    }

    /**
     * Normalize query terms
     *
     * @param  \Slim\PDO\Statement\StatementContainer  $query
     * @param  callable|array|int  $terms
     * @return void
     */
    protected function normalizeTerms(StatementContainer $query, &$terms)
    {
        if (is_callable($terms)) {
            $terms($query);
        } elseif (is_numeric($terms) && !is_float($terms)) {
            $query->where($this->primary, '=', (int) $terms);
        } elseif (is_array($terms)) {
            foreach ($terms as $key => $value) {
                $sign = '=';
                if (strpos($key, ' ') !== false) {
                    list($key, $sign) = explode(' ', $key);
                }

                if (null !== $value) {
                    $query->where($key, $sign, $value);
                } else {
                    $query->whereNull($key);
                }
            }
        }
    }

    /**
     * Generate new data
     *
     * @return  string
     */
    protected function freshDate()
    {
        return date('Y-m-d H:i:s');
    }

    protected function setDatabase(Database $db)
    {
        $this->db = $db;
    }

    protected function setAttributes(array $params = [])
    {
        $this->attributes = $params;
    }

    public function __get($field)
    {
        return isset($this->attributes[$field]) ? $this->attributes[$field] : null;
    }

    public function __set($field, $value)
    {
        $this->attributes[$field] = $value;
    }

    public static function __callStatic($method, $params)
    {
        $model = app()->data(static::class);

        return call_user_func_array([$model, $method], $params);
    }
}
