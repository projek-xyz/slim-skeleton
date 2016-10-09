<?php
namespace Projek\Slim\Database;

use Projek\Slim\Container;
use Slim\PDO\Database;
use Slim\PDO\Statement\StatementContainer;

abstract class Models implements \Countable
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
    protected $softDeletes = false;

    /**
     * @var static
     */
    private static $instance;

    /**
     * @param Database|array|null $attributes
     */
    public function __construct($attributes = null)
    {
        if (is_array($attributes)) {
            $this->attributes = $attributes;
        }

        self::$instance = $this;
    }

    /**
     * Show specific or all data
     *
     * @param  mixed $terms
     * @param  array $columns
     *
     * @return Results|false
     */
    public static function show($terms = null, array $columns = [])
    {
        $self = self::newSelf();

        if (!$self->table()) {
            return false;
        }

        $query = $self->select($columns);

        $self->normalizeTerms($query, $terms);

        $model = $self->attributes? static::class : $self;

        return new Results($query, $model);
    }

    /**
     * Create new data
     *
     * @param  array $pairs
     *
     * @return int|false
     */
    public static function create(array $pairs = null)
    {
        $self = self::newSelf($pairs);

        if (!empty($self->attributes) && null === $pairs) {
            $pairs = $self->attributes;
        }

        if (!$self->table()) {
            return false;
        }

        if (empty($pairs)) {
            throw new \LogicException('Could not create empty data');
        }

        if ($self->timestamps) {
            $pairs[self::CREATED] = $pairs[self::UPDATED] = $self->freshDate();
        }

        $query = $self->insert(array_keys($pairs))->values(array_values($pairs));

        if ($result = $query->execute()) {
            $self->attributes[$self->primary()] = $result;

            return $result;
        }

        return false;
    }

    /**
     * Update spesific data
     *
     * @param  array $pairs
     * @param  mixed $terms
     *
     * @return int|false
     */
    public static function patch($pairs = null, $terms = null)
    {
        $self = self::newSelf();

        if (!$self->table()) {
            return false;
        }

        if (!empty($self->attributes) && null === $terms) {
            $terms = $self->attributes;
        }

        if (empty($pairs)) {
            throw new \LogicException('Could not update empty data');
        }

        if ($self->timestamps) {
            $pairs[self::UPDATED] = $self->freshDate();
        }

        $query = $self->update($pairs);

        $self->normalizeTerms($query, $terms);

        if ($result = $query->execute()) {
            $self->attributes = array_merge($self->attributes, $pairs);

            return $result;
        }

        return 0;
    }

    /**
     * Delete specific data
     *
     * @param  mixed $terms
     *
     * @return int|false
     */
    public static function delete($terms = null)
    {
        $self = static::newSelf($terms);

        if (!empty($self->attributes) && null === $terms) {
            $terms = $self->attributes[$self->primary()];
        }

        if (empty($terms)) {
            throw new \LogicException('Could not delete empty data');
        }

        if ($self->softDeletes) {
            return $self->patch([self::DELETED => $self->freshDate()], $terms);
        }

        if (!$self->table()) {
            return false;
        }

        $query = $self->remove();

        $self->normalizeTerms($query, $terms);

        return $query->execute();
    }

    /**
     * Restore soft-deleted data
     *
     * @param  mixed $terms
     *
     * @return int|false
     */
    public static function restore($terms = null)
    {
        $self = self::newSelf();

        if ($self->softDeletes) {
            return $self->patch([self::DELETED => '0000-00-00 00:00:00'], $terms);
        }

        return false;
    }

    /**
     * Count all data
     *
     * @param  callable|array|int  $terms
     *
     * @return int
     */
    public function count($terms = null)
    {
        if (!$this->table()) {
            return 0;
        }

        $query = $this->select(['count(*) count']);

        $this->normalizeTerms($query, $terms);

        return (int) $query->execute()->fetch()['count'];
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * Get primary key name
     *
     * @return int
     */
    public function primary()
    {
        return $this->primary;
    }

    /**
     * Get primary key value
     *
     * @return int|null
     */
    public function key()
    {
        return !empty($this->attributes) ? $this->attributes[$this->primary()] : null;
    }

    /**
     * Get qualified primary key name
     *
     * @return string
     */
    public function qualifiedPrimary()
    {
        return $this->table().'.'.$this->primary();
    }

    /**
     * Select data from table
     *
     * @param  array $columns
     *
     * @return \Slim\PDO\Statement\SelectStatement
     */
    protected function select(array $columns = [])
    {
        $columns = !is_array($columns) ? func_get_args() : $columns;

        if (empty($columns)) {
            $columns = ['*'];
        }

        return static::db()->select($columns)->from($this->table());
    }

    /**
     * Select data from table
     *
     * @param  array $pairs
     *
     * @return \Slim\PDO\Statement\InsertStatement
     */
    protected function insert($pairs)
    {
        return static::db()->insert($pairs)->into($this->table());
    }

    /**
     * Select data from table
     *
     * @param  array $pairs
     *
     * @return \Slim\PDO\Statement\UpdateStatement
     */
    protected function update($pairs)
    {
        return static::db()->update(array_filter($pairs))->table($this->table());
    }

    /**
     * Select data from table
     *
     * @return \Slim\PDO\Statement\DeleteStatement
     */
    protected function remove()
    {
        return static::db()->delete($this->table);
    }

    /**
     * @param  string|static $model
     * @param  string|null $first
     * @param  string $operator
     * @param  string|null $second
     * @param  string $joinType
     *
     * @return \Slim\PDO\Statement\SelectStatement
     */
    protected function join($model, $first = null, $operator = '=', $second = null, $joinType = 'INNER')
    {
        list($model, $first, $second) = $this->normalizeJoins($model, $first, $second);

        return $this->select()->join($model->table(), $first, $operator, $second, $joinType);
    }

    /**
     * @param  Models|string $model
     * @param  string $first
     * @param  string $second
     *
     * @return array
     */
    protected function normalizeJoins($model, $first = null, $second = null)
    {
        if (is_string($model)) {
            /** @var  Models $model */
            $model = new $model;
        }

        if (!$model instanceof Models) {
            throw new \InvalidArgumentException(sprintf(
                'Expected 1 parameter of %s to be string or %s instance, %s given.',
                __FUNCTION__,
                static::class,
                gettype($model)
            ));
        }

        if (null === $first) {
            $first = $this->primary();
        }

        if (null === $second) {
            $second = $this->table().'_'.$model->primary();
        }

        return [$model, $this->table().'.'.$first, $model->table().'.'.$second];
    }

    /**
     * Normalize query terms
     *
     * @param  \Slim\PDO\Statement\StatementContainer $stmt
     * @param  Models|array|int|callable $terms
     *
     * @return void
     */
    protected function normalizeTerms(StatementContainer $stmt, $terms)
    {
        if ($terms instanceof Models) {
            $terms = $terms->key();
        }

        if (is_callable($terms)) {
            $terms($stmt);
        } elseif (is_numeric($terms) && !is_float($terms)) {
            $stmt->where($this->primary, '=', (int) $terms);
        } elseif (is_array($terms)) {
            foreach ($terms as $key => $value) {
                $sign = '=';
                if (strpos($key, ' ') !== false) {
                    list($key, $sign) = explode(' ', $key);
                }

                if (is_array($value)) {
                    $stmt->whereIn($key, $value);
                } elseif (null === $value) {
                    $stmt->whereNull($key);
                } else {
                    $stmt->where($key, $sign, $value);
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

    /**
     * @return Database
     */
    final protected static function db()
    {
        return Container::instance()->get('db');
    }

    /**
     * @param  null|array $attributes
     *
     * @return static
     */
    final protected static function newSelf($attributes = null)
    {
        return self::$instance ?: new static($attributes);
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
        $model = new static();
        $protected = ['freshDate'];

        if (!in_array($method, $protected) && method_exists($model, $method)) {
            return call_user_func_array([$model, $method], $params);
        }

        throw new \BadMethodCallException(
            sprintf('Undefined method %s in %s.', $method, static::class)
        );
    }
}
