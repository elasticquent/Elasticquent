<?php namespace Elasticquent\Laralastica;

use Elasticquent\Contracts\Query as QueryContract;

class Query implements QueryContract {

    /**
     * The query to be run.
     *
     * @var mixed
     */
    protected $query;

    /**
     * The type of match.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
     * @var string
     */
    protected $type = 'must';

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Set that this query must be matched.
     *
     * @return Query
     */
    public function must()
    {
        return $this->type('must');
    }

    /**
     * Set that this query should be matched.
     *
     * @return Query
     */
    public function should()
    {
        return $this->type('should');
    }

    /**
     * Set that this query must not be matched.
     *
     * @return Query
     */
    public function mustNot()
    {
        return $this->type('must_not');
    }

    /**
     * Set the type of query.
     *
     * @param string $type
     * @return $this
     */
    protected function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Return the query.
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Return the type of match.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}