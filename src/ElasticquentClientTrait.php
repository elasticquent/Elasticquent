<?php

namespace Elasticquent;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Elasticsearch\ClientBuilder;

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
//        dd($config);
        $isAWS = $this->getElasticConfig('ELASTIC_AWS');
        if ($isAWS) {
            $provider = CredentialProvider::fromCredentials(
                new Credentials($config['aws_key'], $config['aws_secret'])
            );
            $handler = new ElasticsearchPhpHandler($config['aws_region'], $provider);

            return ClientBuilder::create()
                ->setHandler($handler)
                ->setHosts($config['hosts'])
                ->build();
        }
        // elasticsearch v2.0 using builder
        if (class_exists('\Elasticsearch\ClientBuilder')) {
            return \Elasticsearch\ClientBuilder::fromConfig($config);
        }

        // elasticsearch v1
        return new \Elasticsearch\Client($config);
    }

}
