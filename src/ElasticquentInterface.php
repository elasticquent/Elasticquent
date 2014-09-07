<?php namespace Elasticquent;

interface ElasticquentInterface
{
    /**
     * Get ElasticSearch Client
     *
     * @return Elasticsearch\Client
     */
    public function getElasticSearchClient();

    /**
     * New Collection
     *
     * @return
     */
    public function newCollection(array $models = array());

    /**
     * Get Index Name
     *
     * @return string
     */
    public function getIndexName();

    /**
     * Get Type Name
     *
     * @return string
     */
    public function getTypeName();

    /**
     * Uses Timestamps In Index
     *
     * @return void
     */
    public function usesTimestampsInIndex();

    /**
     * Get Mapping Properties
     *
     * @return array
     */
    public function getMappingProperties();

    /**
     * Set Mapping Properties
     *
     * @param   array $mappingProperties
     * @return  void
     */
    public function setMappingProperties($mapping);

    /**
     * Get Index Document Data
     *
     * Get the data that ElasticSearch will
     * index for this particular document.
     *
     * @return  array
     */
    public function getIndexDocumentData();

    /**
     * Index Documents
     *
     * Index all documents in an Eloquent model.
     *
     * @param   array $columns
     * @return  void
     */
    public static function addAllToIndex();

    /**
     * Search a Type
     *
     *
     * @return void
     */
    public static function search($query = array());

    /**
     * Add to Search Index
     *
     * @return
     */
    public function addToIndex();

    /**
     * Remove From Search Index
     *
     * @return
     */
    public function removeFromIndex();

    /**
     * Get Search Document
     *
     * Retrieve an ElasticSearch document
     * for this enty.
     *
     * @return
     */
    public function getIndexedDocument();

    /**
     * Get Basic Elasticsearch Params
     *
     * Most Elasticsearch API calls need the index and
     * type passed in a parameter array.
     *
     * @param     bool $getIdIfPossible
     * @return    array
     */
    function getBasicEsParams($getIdIfPossible = true);

     /**
     * Is Elasticsearch Document
     *
     * Is the data in this module sourced
     * from an Elasticsearch document source?
     *
     * @return bool
     */
    public function isDocument();

    /**
     * Get Document Score
     *
     * @return null|float
     */
    public function documentScore();

    /**
     * Put Mapping
     *
     * @param     bool $ignoreConflicts
     * @return
     */
    public static function putMapping($ignoreConflicts = false);

    /**
     * Delete Mapping
     *
     * @return
     */
    public static function deleteMapping();

    /**
     * Rebuild Mapping
     *
     * This will delete and then re-add
     * the mapping for this model.
     *
     * @return
     */
    public static function rebuildMapping();

    /**
     * Get Mapping
     *
     * Get our existing Elasticsearch mapping
     * for this model.
     *
     * @return
     */
    public static function getMapping();

    /**
     * Type Exists
     *
     * Does this type exist?
     *
     * @return bool
     */
    public static function typeExists();

}