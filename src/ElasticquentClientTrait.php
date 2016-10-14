<?php

namespace Elasticquent;

trait ElasticquentClientTrait
{
    use ElasticquentConfigTrait;

    /**
     * Get ElasticSearch Client.
     *
     * @return \Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        $factory = new ElasticSearchClientFactory();

        return $factory->getClient();
    }
}
