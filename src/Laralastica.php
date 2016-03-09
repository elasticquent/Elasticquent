<?php namespace Elasticquent;

use Closure;
use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query as ElasticaQuery;
use Elastica\Query\BoolQuery;
use Elastica\ResultSet;
use Elastica\Search;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Elasticquent\Contracts\Query as QueryContract;
use Elasticquent\Contracts\Wrapper;

class Laralastica implements Wrapper
{

    /**
     * The package config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * An instance of the elastica client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The elastic search index being used.
     *
     * @var Index
     */
    protected $index;

    /**
     * The results from the latest search.
     *
     * @var ResultSet
     */
    protected $results;

    /**
     * The package config.
     *
     * @var array
     */
    protected $sortFields = [];

    public function __construct(array $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;

        $this->client = $this->newClient();
        $this->index = $this->newIndex();
    }

    /**
     * Run the elasticsearch query and then get the corresponding models for
     * the results.
     *
     * @param string|array $types
     * @param callable $query
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function search($types, Closure $query, $limit = null, $offset = null)
    {
        $results = $this->query($types, $query, $limit, $offset);

        return $this->resultsToModels($results);
    }

    /**
     * Run a search and then paginate the results using the laravel length
     * aware paginator.
     *
     * @param string|array $types
     * @param callable $query
     * @param string|int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate($types, Closure $query, $perPage)
    {
        $page = $this->request->has('page') ? $this->request->get('page') : 1;
        $offset = $perPage * ($page - 1);

        // Get the total results
        $this->query($types, $query);

        $total = $this->results->getTotalHits();
        $results = $this->search($types, $query, $perPage, $offset);

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => $this->request->url(),
        ]);
    }

    /**
     * Run a Elastica query and then return the results.
     *
     * @param string|array $types
     * @param callable $query
     * @param null $limit
     * @param null $offset
     * @return ResultSet
     */
    public function query($types, Closure $query, $limit = null, $offset = null)
    {
        $builder = $this->newQueryBuilder();
        $query($builder);

        $search = $this->newSearch($this->client, $this->index, $types);
        $query = $this->newQuery($builder->getQuery());

        if (is_int($limit)) {
            $query->setSize($limit);
        } else {
            $query->setSize($this->config['size']);
        }

        if (is_array($this->sortFields) && count($this->sortFields)) {
            $query->setSort($this->sortFields);
        }
        if (is_int($offset)) {
            $query->setFrom($offset);
        }

        $search->setQuery($query);


        return $this->results = $search->search();
    }

    /**
     * Add a new document to the provided type.
     *
     * @param string $type
     * @param string|int $id
     * @param array $data
     * @return $this
     */
    public function add($type, $id, array $data)
    {
        $type = $this->getType($type);

        $document = new Document($id, $data);
        $type->addDocument($document);

        $this->refreshIndex();

        return $this;
    }

    /**
     * Add multiple documents to the elasticsearch type. The data array must be a
     * multidimensional array with the key as the desired id and the value as
     * the data to be added to the document.
     *
     * @param string $type
     * @param array $data
     * @return $this
     */
    public function addMultiple($type, array $data)
    {
        $type = $this->getType($type);
        $documents = [];

        foreach ($data as $id => $values) {
            $documents[] = new Document($id, $values);
        }

        $type->addDocuments($documents);

        $this->refreshIndex();

        return $this;
    }

    /**
     * Delete a document from the provided type.
     *
     * @param string $type
     * @param string|int $id
     * @return $this
     */
    public function delete($type, $id)
    {
        $type = $this->getType($type);
        $type->deleteById($id);

        $this->refreshIndex();

        return $this;
    }

    /**
     * Return the total results from the last search.
     *
     * @return int
     */
    public function getTotalHits()
    {
        if (isset($this->results)) {
            return $this->results->getTotalHits();
        }
    }

    /**
     * Return the total amount of time for the last search.
     *
     * @return int
     */
    public function getTotalTime()
    {
        if (isset($this->results)) {
            return $this->results->getTotalTime();
        }
    }

    /**
     * Get an elasticsearch type from its index.
     *
     * @param string $type
     * @return \Elastica\Type
     */
    protected function getType($type)
    {
        if (!isset($this->index)) {
            $this->index = $this->newIndex();
        }

        return $this->index->getType($type);
    }

    /**
     * Turn the elasticsearch results into a collection of models.
     *
     * @param ResultSet $resultSet
     * @return Collection
     */
    protected function resultsToModels(ResultSet $resultSet)
    {
        $results = $resultSet->getResults();

        if (!empty($results)) {
            $groupedResults = $this->groupResultsByType($results);
            $modelResults = $this->getModelsFromGroupedResults($groupedResults);
            $collection = $this->newCollection($modelResults);

            return $collection->sortByDesc(function ($model) {
                return $model->score;
            });
        }

        return $this->newCollection([]);
    }

    /**
     * Get th models from the grouped search results.
     *
     * @param $groupedResults
     * @return array
     */
    protected function getModelsFromGroupedResults($groupedResults)
    {
        $modelResults = [];

        foreach ($groupedResults as $key => $results) {
            $modelName = $this->config['types'][$key];
            $model = new $modelName;
            $query = $model->whereIn('id', array_keys($results))
                ->with($model::getEagerLoaded())
                ->orderBy(\DB::raw('FIELD(id, ' . implode(',', array_keys($results)) . ')'), 'ASC')
                ->get();

            $query->map(function ($model) use ($results) {
                $model->score = $results[$model->getKey()]->getScore();
            });

            $modelResults = array_merge_recursive($modelResults, $query->all());
        }

        return $modelResults;
    }

    /**
     * Group the
     *
     * @param array $results
     * @return array
     */
    protected function groupResultsByType(array $results)
    {
        $groupedResults = [];

        foreach ($results as $result) {
            if (!isset($groupedResults[$result->getType()])) {
                $groupedResults[$result->getType()] = [];
            }

            $groupedResults[$result->getType()][$result->getId()] = $result;
        }

        return $groupedResults;
    }

    /**
     * Create a new elastica client.
     *
     * @return Client
     */
    protected function newClient()
    {
        $config = config('elasticquent')['config'];
        if (class_exists('\Elasticsearch\ClientBuilder')) {
            return \Elasticsearch\ClientBuilder::fromConfig($config);
        }
        return new Client($this->connection());
    }

    /**
     * Get the elastica connection config.
     *
     * @return array
     */
    protected function connection()
    {
        return [
            'host' => !empty($this->config['host']) ? $this->config['host'] : null,
            'port' => !empty($this->config['port']) ? $this->config['port'] : null,
            'url'  => !empty($this->config['url']) ? $this->config['url'] : null,
        ];
    }

    /**
     * Get the elasticsearch index being used.
     *
     * @return Index
     */
    protected function newIndex()
    {
        if (!isset($this->client)) {
            $this->client = $this->newClient();
        }

        return $this->client->getIndex($this->config['index']);
    }

    /**
     * Create a new laralastica query builder.
     *
     * @return Builder
     */
    public  function newQueryBuilder()
    {
        return new Builder($this->config);
    }

    /**
     * Create a new elastica search.
     *
     * @param Client $client
     * @param Index $index
     * @param string|array $types
     * @return Search
     */
    protected function newSearch(Client $client, Index $index, $types)
    {
        if (is_string($types)) {
            $types = [$types];
        }

        $search = new Search($client);

        $search->addIndex($index);
        $search->addTypes($types);

        return $search;
    }

    /**
     * Create a new elastica query from an array of queries.
     *
     * @param array $queries
     * @return Query
     */
    public function newQuery(array $queries)
    {
        if (!empty($queries)) {
            $container = new BoolQuery();

            foreach ($queries as $query) {
                $container = $this->addQueryToContainer($query, $container);
            }

            $query = new ElasticaQuery($container);
            $query->addSort('_score');
        } else {
            $query = new ElasticaQuery();
        }

        return $query;
    }

    /**
     * Set the type of match for the query and then add it to the bool container.
     *
     * @param QueryContract $query
     * @param Bool $container
     * @return Bool
     */
    protected function addQueryToContainer(QueryContract $query, BoolQuery $container)
    {
        switch ($query->getType()) {
            case "must":
                $container->addMust($query->getQuery());
                break;

            case "should":
                $container->addShould($query->getQuery());
                break;

            case "must_not":
                $container->addMustNot($query->getQuery());
                break;
        }

        return $container;
    }

    /**
     * Create a new collection.
     *
     * @param array $data
     * @return Collection
     */
    protected function newCollection(array $data)
    {
        return new Collection($data);
    }

    /**
     * Refreshes the elasticsearch index, should be run after adding
     * or deleting documents.
     *
     * @return \Elastica\Response
     */
    protected function refreshIndex()
    {
        return $this->index->refresh();
    }

    /**
     * Sort on multiple fields.
     *
     * @param array $fields Associative array where the keys are field names to sort on, and the
     *                      values are the sort order: "asc" or "desc"
     *
     * @return array
     */

    public function setSortFields(array $fields)
    {
        return $this->sortFields = $fields;
    }

}