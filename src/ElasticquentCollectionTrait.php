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

        $params = array();
        $params = [
            'client' => [
                'curl' => [
                    CURLOPT_HTTPHEADER => [
                        'Content-type: application/json',
                    ]
                ]
            ]
        ];
        foreach ($this->all() as $item) {
            $params['body'][] = array(
                'index' => array(
                    '_id' => $item->getKey(),
                    '_type' => $item->getTypeName(),
                    '_index' => $item->getIndexName(),
                ),
            );

            $params['body'][] = $item->getIndexDocumentData();
        }
//        dd($params);
        return $this->getElasticSearchClient()->bulk($params);
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
        $params = [
            'client' => [
                'curl' => [
                    CURLOPT_HTTPHEADER => [
                        'Content-type: application/json',
                    ]
                ]
            ]
        ];
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
