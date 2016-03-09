<?php namespace Elasticquent\Contracts;

interface Query {

    /**
     * Set that this query must be matched.
     *
     * @return \Elasticquent\Query
     */
    public function must();

    /**
     * Set that this query should be matched.
     *
     * @return \Elasticquent\Query
     */
    public function should();

    /**
     * Set that this query must not be matched.
     *
     * @return \Elasticquent\Query
     */
    public function mustNot();

    /**
     * Return the query.
     *
     * @return mixed
     */
    public function getQuery();

    /**
     * Return the type of match.
     *
     * @return string
     */
    public function getType();

}