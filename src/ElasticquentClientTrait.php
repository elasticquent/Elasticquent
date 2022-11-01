<?php

namespace Elasticquent;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ConfigException;

trait ElasticquentClientTrait
{
    use ElasticquentConfigTrait;

    /**
     * Get ElasticSearch Client
     *
     * @return \Elastic\Elasticsearch\Client
     * @throws ConfigException
     */
    public function getElasticSearchClient()
    {
        $config = $this->getElasticConfig();


        return ClientBuilder::fromConfig($config);
    }

}
