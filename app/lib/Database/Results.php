<?php
namespace Projek\Slim\Database;

use Slim\PDO\Statement\SelectStatement;

class Results implements \Countable
{
    /**
     * @var  mixed
     */
    protected $statement;

    /**
     * @var  string
     */
    protected $query;

    /**
     * @var  array
     */
    protected $params;

    /**
     * @var  \PDOStatement
     */
    protected $results;

    /**
     * @var  string
     */
    protected $modelClass;

    /**
     * @param  SelectStatement $statement
     * @param  string $modelClass
     */
    public function __construct(SelectStatement $statement, $modelClass = '')
    {
        /** @var  SelectStatement $statement */
        $this->statement = $statement;
        $this->query = $statement->compile();
        $this->modelClass = $modelClass;
    }

    /**
     * @param  int|null $limit
     * @param  int|null $offset
     *
     * @return \PDOStatement
     */
    protected function fetch($limit = null, $offset = null)
    {
        if (is_int($limit)) {
            $this->statement->limit($limit, $offset);
        }

        $statement = $this->statement->execute();

        $statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $this->modelClass);

        return $statement;
    }

    /**
     * @return Models
     */
    public function get()
    {
        return $this->fetch()->fetch();
    }

    /**
     * @return array
     */
    public function all()
    {
        $this->results = $this->fetch();

        return $this->results->fetchAll();
    }

    /**
     * @param  int|null $limit
     * @param  int|null $offset
     *
     * @return array
     */
    public function limit($limit = null, $offset = null)
    {
        $this->results = $this->fetch($limit, $offset);

        return $this->results->fetchAll();
    }

    /**
     * @return int
     */
    public function count()
    {
        if (!$this->results) {
            $this->results = $this->fetch();
        }

        return $this->results->rowCount();
    }
}
