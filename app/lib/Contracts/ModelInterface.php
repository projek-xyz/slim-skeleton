<?php

namespace Projek\Slim\Contracts;

interface ModelInterface extends \Countable
{
    const UPDATED = 'updated_at';
    const CREATED = 'created_at';
    const DELETED = 'deleted_at';

    /**
     * Get basic data
     *
     * @param string[]           $columns Array of column
     * @param callable|array|int $terms   column value pairs of term data you wanna find to
     * @return \PDOStatement|false
     */
    public function show($terms = null, array $columns = []);

    /**
     * Create new data
     *
     * @param array $pairs column value pairs of data
     * @return int|false
     */
    public function create(array $pairs);

    /**
     * Update existing item from table
     *
     * @param array              $pairs column value pairs of data
     * @param callable|array|int $terms column value pairs of term data you wanna update to
     * @return int|false
     */
    public function edit(array $pairs, $terms = null);

    /**
     * Delete Item from table
     *
     * @param  callable|array|int  $terms
     * @return int
     */
    public function remove($terms);

    /**
     * Retrieve table primary key
     *
     * @return string
     */
    public function primary();
}
