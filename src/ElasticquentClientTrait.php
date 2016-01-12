<?php

namespace Elasticquent;

trait ElasticquentClientTrait
{
    use ElasticquentConfigTrait;

    /**
     * Get ElasticSearch Client
     *
     * @return \Elasticsearch\Client
     */
    public function getElasticSearchClient()
    {
        $config = $this->getElasticConfig();

        // elasticsearch v2.0 using builder
        if (class_exists('\Elasticsearch\ClientBuilder')) {
            return \Elasticsearch\ClientBuilder::fromConfig($config);
        }

        // elasticsearch v1
        return new \Elasticsearch\Client($config);
    }

}
