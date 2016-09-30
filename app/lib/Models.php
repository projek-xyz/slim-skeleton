<?php
namespace Projek\Slim;

use Slim\PDO\Database;
use Slim\PDO\Statement\StatementContainer;

/**
 * @method static \PDOStatement|false get(mixed $terms = null, array $columns = [])
 * @method static int|false add(array $pairs)
 * @method static int|false put(array $pairs, $terms = null)
 * @method static int|false del(mixed $terms)
 */
abstract class Models
{
    const UPDATED = 'updated_at';
    const CREATED = 'created_at';
    const DELETED = 'deleted_at';

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
     * @var bool
     */
    protected $destructive = false;

    /**
     * @param Database|array|null $props
     */
    public function __construct($props = null)
    {
        if ($props instanceof Database) {
            $this->db = $props;
        } elseif (is_array($props)) {
            $this->attributes = $props;
        }
    }

    /**
     * @inheritdoc
     */
    public function show($terms = null, array $columns = [])
    {
        if (!$this->table) {
            return false;
        }

        $query = $this->select($columns);

        $this->normalizeTerms($query, $terms);

        $stmt = $query->execute();

        $stmt->setFetchMode(\PDO::FETCH_INTO, $this);

        return $stmt;
    }

    /**
     * @inheritdoc
     */
    public function create(array $pairs = null)
    {
        if (!$this->table) {
            return false;
        }

        if (null === $pairs) {
            $pairs = $this->attributes;
            $this->db = app('db');
        }

        if (empty($pairs)) {
            throw new \LogicException('Could not create empty data');
        }

        if ($this->timestamps) {
            $pairs[self::CREATED] = $pairs[self::UPDATED] = $this->freshDate();
        }

        $query = $this->insert(array_keys($pairs))->values(array_values($pairs));

        return (int) $query->execute(true);
    }

    /**
     * @inheritdoc
     */
    public function edit($pairs = null, $terms = null)
    {
        if (!$this->table) {
            return false;
        }

        if (!empty($this->attributes) && null === $terms) {
            $terms = $this->attributes;
            $this->db = app('db');
        }

        if (empty($pairs)) {
            throw new \LogicException('Could not update empty data');
        }

        if ($this->timestamps) {
            $pairs[self::UPDATED] = $this->freshDate();
        }

        $query = $this->update($pairs);

        $this->normalizeTerms($query, $terms);

        return (bool) $query->execute();
    }

    /**
     * @inheritdoc
     */
    public function remove($terms)
    {
        if (false === $this->destructive) {
            return $this->edit([self::DELETED => $this->freshDate()], $terms);
        }

        if (!$this->table) {
            return false;
        }

        $query = $this->delete();

        $this->normalizeTerms($query, $terms);

        return (bool) $query->execute();
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

        $query = $this->select(['count(*) count']);

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
     * Select data from table
     *
     * @param  array $columns
     * @return \Slim\PDO\Statement\SelectStatement
     */
    protected function select(array $columns = [])
    {
        $columns = !is_array($columns) ? func_get_args() : $columns;

        if (empty($columns)) {
            $columns = ['*'];
        }

        return $this->db->select($columns)->from($this->table);
    }

    /**
     * Select data from table
     *
     * @param  array $pairs
     * @return \Slim\PDO\Statement\InsertStatement
     */
    protected function insert($pairs)
    {
        return $this->db->insert($pairs)->into($this->table);
    }

    /**
     * Select data from table
     *
     * @param  array $pairs
     * @return \Slim\PDO\Statement\UpdateStatement
     */
    protected function update($pairs)
    {
        return $this->db->update(array_filter($pairs))->table($this->table);
    }

    /**
     * Select data from table
     *
     * @return \Slim\PDO\Statement\DeleteStatement
     */
    protected function delete()
    {
        return $this->db->delete($this->table);
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
        $data = app('data');
        $model = $data(static::class);
        $aliases = [
            'get' => 'show',
            'add' => 'create',
            'put' => 'edit',
            'del' => 'remove',
        ];

        if (isset($aliases[$method])) {
            $method = $aliases[$method];
        }

        return call_user_func_array([$model, $method], $params);
    }
}
