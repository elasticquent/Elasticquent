<?php

namespace Elasticquent;

use Exception;

/**
 * Elasticquent Trait
 *
 * Functionality extensions for Elequent that
 * makes working with Elasticsearch easier.
 */
trait ElasticquentTrait
{
    use ElasticquentClientTrait, EloquentMethods;

    /**
     * Uses Timestamps In Index
     *
     * @var bool
     */
    protected $usesTimestampsInIndex = true;

    /**
     * Is ES Document
     *
     * Set to true when our model is
     * populated by a
     *
     * @var bool
     */
    protected $isDocument = false;

    /**
     * Document Score
     *
     * Hit score when using data
     * from Elasticsearch results.
     *
     * @var null|int
     */
    protected $documentScore = null;

    /**
     * Document Version
     *
     * Elasticsearch document version.
     *
     * @var null|int
     */
    protected $documentVersion = null;

    /**
     * Mapping Properties
     *
     * Elasticsearch mapping properties
     *
     * @var null|array
     */
    protected $mappingProperties = null;

    /**
     * New Collection
     *
     * @param array $models
     * @return ElasticquentCollection
     */
    public function newCollection(array $models = array())
    {
        return new ElasticquentCollection($models);
    }

    /**
     * Get Index Name
     *
     * @return string
     */
    public function getIndexName()
    {
        // The first thing we check is if there is an elasticquent
        // config file and if there is a default index.
        $index_name = $this->getElasticConfig('default_index');

        if (!empty($index_name)) {
            return $index_name;
        }

        // Otherwise we will just go with 'default'
        return 'default';
    }

    /**
     * Get Type Name
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->getTable();
    }

    /**
     * Uses Timestamps In Index.
     */
    public function usesTimestampsInIndex()
    {
        return $this->usesTimestampsInIndex;
    }

    /**
     * Use Timestamps In Index.
     */
    public function useTimestampsInIndex($shouldUse = true)
    {
        $this->usesTimestampsInIndex = $shouldUse;
    }

    /**
     * Don't Use Timestamps In Index.
     *
     * @deprecated
     */
    public function dontUseTimestampsInIndex()
    {
        $this->useTimestampsInIndex(false);
    }

    /**
     * Get Mapping Properties
     *
     * @return array
     */
    public function getMappingProperties()
    {
        return $this->mappingProperties;
    }

    /**
     * Set Mapping Properties
     *
     * @param    array $mapping
     * @internal param array $mapping
     */
    public function setMappingProperties(array $mapping = null)
    {
        $this->mappingProperties = $mapping;
    }

    /**
     * Is Elasticsearch Document
     *
     * Is the data in this module sourced
     * from an Elasticsearch document source?
     *
     * @return bool
     */
    public function isDocument()
    {
        return $this->isDocument;
    }

    /**
     * Get Document Score
     *
     * @return null|float
     */
    public function documentScore()
    {
        return $this->documentScore;
    }

    /**
     * Document Version
     *
     * @return null|int
     */
    public function documentVersion()
    {
        return $this->documentVersion;
    }

    /**
     * Get Index Document Data
     *
     * Get the data that Elasticsearch will
     * index for this particular document.
     *
     * @return array
     */
    public function getIndexDocumentData()
    {
        return $this->toArray();
    }

    /**
     * Index Documents
     *
     * Index all documents in an Eloquent model.
     *
     * @return array
     */
    public static function addAllToIndex()
    {
        $instance = new static;

        $all = $instance->newQuery()->get(array('*'));

        return $all->addToIndex();
    }

    /**
     * Re-Index All Content
     *
     * @return array
     */
    public static function reindex()
    {
        $instance = new static;

        $all = $instance->newQuery()->get(array('*'));

        return $all->reindex();
    }

    /**
     * Search By Query
     *
     * Search with a query array
     *
     * @param array $query
     * @param array $aggregations
     * @param array $sourceFields
     * @param int   $limit
     * @param int   $offset
     * @param array $sort
     *
     * @return ElasticquentResultCollection
     */
    public static function searchByQuery($query = null, $aggregations = null, $sourceFields = null, $limit = null, $offset = null, $sort = null)
    {
        $instance = new static;

        $params = $instance->getBasicEsParams(true, true, true, $limit, $offset);

        if (!empty($sourceFields)) {
            $params['body']['_source']['include'] = $sourceFields;
        }

        if (!empty($query)) {
            $params['body']['query'] = $query;
        }

        if (!empty($aggregations)) {
            $params['body']['aggs'] = $aggregations;
        }

        if (!empty($sort)) {
            $params['body']['sort'] = $sort;
        }

        $result = $instance->getElasticSearchClient()->search($params);

        return new \Elasticquent\ElasticquentResultCollection($result, $instance = new static);
    }

    /**
     * Perform a "complex" or custom search.
     *
     * Using this method, a custom query can be sent to Elasticsearch.
     *
     * @param  $params parameters to be passed directly to Elasticsearch
     * @return ElasticquentResultCollection
     */
    public static function complexSearch($params)
    {
        $instance = new static;

        $result = $instance->getElasticSearchClient()->search($params);

        return new \Elasticquent\ElasticquentResultCollection($result, $instance = new static);
    }

    /**
     * Search
     *
     * Simple search using a match _all query
     *
     * @param string $term
     *
     * @return ElasticquentResultCollection
     */
    public static function search($term = '')
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        $params['body']['query']['match']['_all'] = $term;

        $result = $instance->getElasticSearchClient()->search($params);

        return new \Elasticquent\ElasticquentResultCollection($result, $instance = new static);
    }

    /**
     * Add to Search Index
     *
     * @throws Exception
     * @return array
     */
    public function addToIndex()
    {
        if (!$this->exists) {
            throw new Exception('Document does not exist.');
        }

        $params = $this->getBasicEsParams();

        // Get our document body data.
        $params['body'] = $this->getIndexDocumentData();

        // The id for the document must always mirror the
        // key for this model, even if it is set to something
        // other than an auto-incrementing value. That way we
        // can do things like remove the document from
        // the index, or get the document from the index.
        $params['id'] = $this->getKey();

        return $this->getElasticSearchClient()->index($params);
    }

    /**
     * Remove From Search Index
     *
     * @return array
     */
    public function removeFromIndex()
    {
        return $this->getElasticSearchClient()->delete($this->getBasicEsParams());
    }

    /**
     * Partial Update to Indexed Document
     *
     * @return array
     */
    public function updateIndex()
    {
        $params = $this->getBasicEsParams();

        // Get our document body data.
        $params['body']['doc'] = $this->getIndexDocumentData();

        return $this->getElasticSearchClient()->update($params);
    }

    /**
     * Get Search Document
     *
     * Retrieve an ElasticSearch document
     * for this enty.
     *
     * @return array
     */
    public function getIndexedDocument()
    {
        return $this->getElasticSearchClient()->get($this->getBasicEsParams());
    }

    /**
     * Get Basic Elasticsearch Params
     *
     * Most Elasticsearch API calls need the index and
     * type passed in a parameter array.
     *
     * @param bool $getIdIfPossible
     * @param bool $getSourceIfPossible
     * @param bool $getTimestampIfPossible
     * @param int  $limit
     * @param int  $offset
     *
     * @return array
     */
    public function getBasicEsParams($getIdIfPossible = true, $getSourceIfPossible = false, $getTimestampIfPossible = false, $limit = null, $offset = null)
    {
        $params = array(
            'index' => $this->getIndexName(),
            'type' => $this->getTypeName(),
        );

        if ($getIdIfPossible && $this->getKey()) {
            $params['id'] = $this->getKey();
        }

        $fields = $this->buildFieldsParameter($getSourceIfPossible, $getTimestampIfPossible);
        if (!empty($fields)) {
            $params['fields'] = implode(',', $fields);
        }

        if (is_numeric($limit)) {
            $params['size'] = $limit;
        }

        if (is_numeric($offset)) {
            $params['from'] = $offset;
        }

        return $params;
    }

    /**
     * Build the 'fields' parameter depending on given options.
     *
     * @param bool   $getSourceIfPossible
     * @param bool   $getTimestampIfPossible
     * @return array
     */
    private function buildFieldsParameter($getSourceIfPossible, $getTimestampIfPossible)
    {
        $fieldsParam = array();

        if ($getSourceIfPossible) {
            $fieldsParam[] = '_source';
        }

        if ($getTimestampIfPossible) {
            $fieldsParam[] = '_timestamp';
        }

        return $fieldsParam;
    }

    /**
     * Mapping Exists
     *
     * @return bool
     */
    public static function mappingExists()
    {
        $instance = new static;

        $mapping = $instance->getMapping();

        return (empty($mapping)) ? false : true;
    }

    /**
     * Get Mapping
     *
     * @return void
     */
    public static function getMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $instance->getElasticSearchClient()->indices()->getMapping($params);
    }

    /**
     * Put Mapping.
     *
     * @param bool $ignoreConflicts
     *
     * @return array
     */
    public static function putMapping($ignoreConflicts = false)
    {
        $instance = new static;

        $mapping = $instance->getBasicEsParams();

        $params = array(
            '_source' => array('enabled' => true),
            'properties' => $instance->getMappingProperties(),
        );

        $mapping['body'][$instance->getTypeName()] = $params;

        return $instance->getElasticSearchClient()->indices()->putMapping($mapping);
    }

    /**
     * Delete Mapping
     *
     * @return array
     */
    public static function deleteMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $instance->getElasticSearchClient()->indices()->deleteMapping($params);
    }

    /**
     * Rebuild Mapping
     *
     * This will delete and then re-add
     * the mapping for this model.
     *
     * @return array
     */
    public static function rebuildMapping()
    {
        $instance = new static;

        // If the mapping exists, let's delete it.
        if ($instance->mappingExists()) {
            $instance->deleteMapping();
        }

        // Don't need ignore conflicts because if we
        // just removed the mapping there shouldn't
        // be any conflicts.
        return $instance->putMapping();
    }

    /**
     * Create Index
     *
     * @param int $shards
     * @param int $replicas
     *
     * @return array
     */
    public static function createIndex($shards = null, $replicas = null)
    {
        $instance = new static;

        $client = $instance->getElasticSearchClient();

        $index = array(
            'index' => $instance->getIndexName(),
        );

        if (!is_null($shards)) {
            $index['body']['settings']['number_of_shards'] = $shards;
        }

        if (!is_null($replicas)) {
            $index['body']['settings']['number_of_replicas'] = $replicas;
        }

        return $client->indices()->create($index);
    }

    /**
     * Delete Index
     *
     * @return array
     */
    public static function deleteIndex()
    {
        $instance = new static;

        $client = $instance->getElasticSearchClient();

        $index = array(
            'index' => $instance->getIndexName(),
        );

        return $client->indices()->delete($index);
    }

    /**
     * Type Exists.
     *
     * Does this type exist?
     *
     * @return bool
     */
    public static function typeExists()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $instance->getElasticSearchClient()->indices()->existsType($params);
    }

    /**
     * New From Hit Builder
     *
     * Variation on newFromBuilder. Instead, takes
     *
     * @param array $hit
     *
     * @return static
     */
    public function newFromHitBuilder($hit = array())
    {
        $instance = $this->newInstance(array(), true);

        $attributes = $hit['_source'];

        // Add fields to attributes
        if (isset($hit['fields'])) {
            foreach ($hit['fields'] as $key => $value) {
                $attributes[$key] = $value;
            }
        }

        $instance->setRawAttributes((array)$attributes, true);

        // In addition to setting the attributes
        // from the index, we will set the score as well.
        $instance->documentScore = $hit['_score'];

        // This is now a model created
        // from an Elasticsearch document.
        $instance->isDocument = true;

        // Set our document version if it's
        if (isset($hit['_version'])) {
            $instance->documentVersion = $hit['_version'];
        }

        return $instance;
    }

}
