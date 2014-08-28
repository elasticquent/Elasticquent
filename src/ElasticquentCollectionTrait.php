<?php namespace Elasticquent;


/**
 * Elasticquent Collection Trait
 *
 * Elasticsearch functions that you
 * can run on collections of documents.
 */
trait ElasticquentCollectionTrait {

    /**
     * Add To Index
     *
     * Add all documents in this collection to
     * to the Elasticsearch document index.
     *
     * @return mixed
     */
    public function addToIndex()
    {
        if ($this->isEmpty()) {
            return null;
        }

        $params = array();

        foreach ($this->all() as $item) {

            $params['body'][] = array(
                'index' => array(
                    '_id' => $item->getKey()
                )
            );

            $params['body'][] = array(
                'doc' => $item->getIndexDocumentData()
            );
        }

       return $this->getElasticSearchClient()->bulk($params);
    }

    /**
     * Get ElasticSearch Client
     *
     * @return Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        return new Elasticsearch\Client();
    }

}
