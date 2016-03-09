<?php namespace Elasticquent\Contracts;

use Closure;
use Elastica\Multi\ResultSet;
use Illuminate\Pagination\LengthAwarePaginator;
use Michaeljennings\Laralastica\Laralastica;

interface Wrapper {

    /**
     * Run the provided queries on the types and then return the results.
     *
     * @param string|array $types
     * @param callable $query
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function search($types, Closure $query, $limit = null, $offset = null);

    /**
     * Run a search and then paginate the results using the laravel length
     * aware paginator.
     *
     * @param string|array $types
     * @param callable $query
     * @param string|int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($types, Closure $query, $perPage);

    /**
     * Run a Elastica query and then return the results.
     *
     * @param string|array $types
     * @param callable $query
     * @param null $limit
     * @param null $offset
     * @return ResultSet
     */
    public function query($types, Closure $query, $limit = null, $offset = null);

    /**
     * Add a new document to the provided type.
     *
     * @param string $type
     * @param string|int $id
     * @param array $data
     * @return $this
     */
    public function add($type, $id, array $data);

    /**
     * Add multiple documents to the elasticsearch type. The data array must be a
     * multidimensional array with the key as the desired id and the value as
     * the data to be added to the document.
     *
     * @param string $type
     * @param array $data
     * @return $this
     */
    public function addMultiple($type, array $data);

    /**
     * Delete a document from the provided type.
     *
     * @param string $type
     * @param string|int $id
     * @return $this
     */
    public function delete($type, $id);

    /**
     * Return the total results from the last search.
     *
     * @return int
     */
    public function getTotalHits();

    /**
     * Return the total amount of time for the last search.
     *
     * @return int
     */
    public function getTotalTime();

}