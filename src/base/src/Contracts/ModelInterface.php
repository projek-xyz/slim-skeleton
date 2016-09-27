<?php

namespace Projek\Slim\Contracts;

interface ModelInterface extends \Countable
{
    const UPDATED = 'updated_at';
    const CREATED = 'updated_at';

    /**
     * Create new data
     *
     * @param array $pairs column value pairs of data
     * @return int|false
     */
    public function create(array $pairs);

    /**
     * Insert batch data into table
     *
     * @param  array  $data
     * @return int
     */
    public function insert(array $data);

    /**
     * Get basic data
     *
     * @param string[]           $columns Array of column
     * @param callable|array|int $terms   column value pairs of term data you wanna find to
     * @return \PDOStatement|false
     */
    public function get(array $columns = [], $terms = null);

    /**
     * Find existing item(s) from table
     *
     * @param callable|array|int $terms column value pairs of term data you wanna find to
     * @return \PDOStatement|false
     */
    public function find($terms = null);

    /**
     * Update existing item from table
     *
     * @param array              $pairs column value pairs of data
     * @param callable|array|int $terms column value pairs of term data you wanna update to
     * @return int|false
     */
    public function update(array $pairs, $terms = null);

    /**
     * Delete Item from table
     *
     * @param  callable|array|int  $terms
     * @return int
     */
    public function delete($terms);

    /**
     * Retrieve table primary key
     *
     * @return string
     */
    public function primary();
}