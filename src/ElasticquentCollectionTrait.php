<?php namespace Elasticquent;

/**
 * Elasticquent Collection Trait
 *
 * Elasticsearch functions that you
 * can run on collections of documents.
 */
trait ElasticquentCollectionTrait
{
    use ElasticquentClientTrait;

    /**
     * @var int The number of records (ie. models) to send to Elasticsearch in one go
     * Also, the number of models to get from the database at a time using Eloquent's chunk()
     */
    static public $entriesToSendToElasticSearchInOneGo = 500;

    /**
     * Add To Index
     *
     * Add all documents in this collection to to the Elasticsearch document index.
     *
     * @return null|array
     */
    public function addToIndex()
    {
        if ($this->isEmpty()) {
            return null;
        }

        // Use an stdClass to store result of elasticsearch operation
        $result = new \stdClass;

        // Iterate according to the amount configured, and put that iteration's worth of records into elastic search
        // This is done so that we do not exceed the maximum request size
        $chunkingResult = $this->chunk(static::$entriesToSendToElasticSearchInOneGo)->each(function ($collectionChunk) use ($result) {
            $params = array();
            foreach ($collectionChunk as $item) {
                $params['body'][] = array(
                    'index' => array(
                        '_id' => $item->getKey(),
                        '_type' => $item->getTypeName(),
                        '_index' => $item->getIndexName(),
                    ),
                );

                $params['body'][] = $item->getIndexDocumentData();
            }

            $result->result = $this->getElasticSearchClient()->bulk($params);

            // Check for errors
            if ( (array_key_exists('errors', $result) && $result['errors'] != false ) || (array_key_exists('Message', $result) && stristr('Request size exceeded', $result['Message']) !== false)) {
                return false;
            }

            // Remove vars immediately to prevent them hanging around in memory, in case we have a large number of iterations
            unset($collectionChunk, $params);
        });

        // Get the result or null it
        if ($chunkingResult && property_exists($result, 'result')) {
            $result = $result->result;
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Delete From Index
     *
     * @return array
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
                    '_index' => $item->getIndexName(),
                ),
            );
        }

        return $this->getElasticSearchClient()->bulk($params);
    }

    /**
     * Reindex
     *
     * Delete the items and then re-index them.
     *
     * @return array
     */
    public function reindex()
    {
        $this->deleteFromIndex();
        return $this->addToIndex();
    }

}
