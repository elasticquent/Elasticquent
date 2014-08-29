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
                    '_id' => $item->getKey(),
                    '_type' => $item->getTypeName(),
                    '_index' => $item->getIndexName()
                )
            );

            $params['body'][] = $item->getIndexDocumentData();
        }

        return $this->getElasticSearchClient()->bulk($params);
    }

    /**
     * 
     *
     * @return void
     */
    public function deleteFromIndex()
    {
        $all = $this->all();

        $params = array();

        foreach ($all as $item) {

            $params['body'][] = array(
                'delete' => array(
                    '_id' => $item->getKey(),
                    '_type' => $item->getTypeName(),
                    '_index' => $item->getIndexName()
                )
            );
        }

        return $this->getElasticSearchClient()->bulk($params);
    }

    /**
     * Get ElasticSearch Client
     *
     * @return \Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        return new \Elasticsearch\Client();
    }

}
