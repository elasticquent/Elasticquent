<?php namespace Adamfairholm\Elasticquent;

/**
 * Elasticquent Trait
 *
 * Functionality extensions for Elequent that
 * makes working with Elasticsearch easier.
 */
trait ElasticquentTrait {

    /**
     * Mapping Properties
     *
     * ElasticSearch mapping properties for this
     * particular model.
     *
     * @var     array
     */
    protected $mappingProperties;

    /**
     * Enable Document Source
     *
     *
     * @var     bool
     */
    protected $enableDocumentSource = true;

    /**
     *
     *
     * @return
     */
    protected $usesTimestampsInIndex = true;

    /**
     * Get ElasticSearch Client
     *
     * @return Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        return new Elasticsearch\Client();
    }

    /**
     * New Collection
     *
     * @return
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
        // The first thing we check is if there
        // is an elasticquery config file and if there is a
        // default index.
        if (Config::get('elasticquent.default_index')) {
            return Config::get('elasticquent.default_index');
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
     *
     *
     * @return void
     */
    public function usesTimestampsInIndex()
    {
        return $this->usesTimestampsInIndex;
    }

    /**
     *
     *
     * @return void
     */
    public function useTimestampsInIndex()
    {
        $this->usesTimestampsInIndex = true;
    }

    /**
     *
     *
     * @return void
     */
    public function dontUseTimestampsInIndex()
    {
        $this->usesTimestampsInIndex = false;
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
     * @param   array $mappingProperties
     * @return  vodi
     */
    public function setMappingProperties($mapping)
    {
        $this->mappingProperties = $mapping;
    }

    /**
     * Get Index Document Data
     *
     * Get the data that ElasticSearch will
     * index for this particular document.
     *
     * @return  array
     */
    public function getIndexDocumentData()
    {
        return $this->toArray();
    }

    /**
     * Get Index Document Routing
     *
     * Get the routing string for this document.
     *
     * @return void
     */
    public function getIndexDocumentRouting()
    {
        return null;
    }

    /**
     * Index Documents
     *
     * Index all documents in an Eloquent model.
     *
     * @param   array $columns
     * @return  void
     */
    public static function addAllToIndex($columns = array('*'))
    {
        $instance = new static;

        $all = $instance->newQuery()->get($columns);


    }

    /**
     * Search a Type
     *
     *
     * @return void
     */
    public static function search($query = array())
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();


    }

    /**
     * Add to Search Index
     *
     * @return
     */
    public function addToIndex()
    {
        if ( ! $this->exists) {
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
     * @return
     */
    public function removeFromIndex()
    {
        $this->getElasticSearchClient()->delete($this->getBasicEsParams());
    }

    /**
     * Get Search Document
     *
     * Retrieve an ElasticSearch document
     * for this enty.
     *
     * @return
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
     * @param     bool $getIdIfPossible
     * @return    array
     */
    public function getBasicEsParams($getIdIfPossible = true)
    {
        $params = array(
            'index'     => $this->getIndexName(),
            'type'      => $this->getTypeName()
        );

        if ($getIdIfPossible and $this->getKey()) {
            $params['id'] = $this->getKey();
        }

        return $params;
    }

    /**
     * Put Mapping
     *
     * @param     bool $ignoreConflicts
     * @return
     */
    public static function putMapping($ignoreConflicts = false)
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        $params['ignore_conflicts'] = $ignoreConflicts;

        $params['_source'] = array('enabled' => $instance->enableDocumentSource);

        $params['properties'] = $instance->getMapping();

        return $instance->getElasticSearchClient()->indices()->putMapping($params);
    }

    /**
     * Delete Mapping
     *
     * @return
     */
    public static function deleteMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();

        return $this->getElasticSearchClient()->indices()->deleteMapping($params);
    }

    /**
     * Rebuild Mapping
     *
     * This will delete and then re-add
     * the mapping for this model.
     *
     * @return
     */
    public function rebuildMapping()
    {
        self::deleteMapping();

        // Don't need ignore conflicts because if we
        // just removed the mapping there shouldn't
        // be any conflicts.
        self::putMapping();
    }

    /**
     * Get Mapping
     *
     * Get our existing Elasticsearch mapping
     * for this model.
     *
     * @return
     */
    public static function getMapping()
    {
        $instance = new static;

        $params = $instance->getBasicEsParams();
    }

    /**
     * Type Exists
     *
     * Does this type exist?
     *
     * @return bool
     */
    public static function typeExists()
    {
        $params = $this->getBasicEsParams();
        return $this->getElasticSearchClient()->indices()->existsType($params);
    }
}
