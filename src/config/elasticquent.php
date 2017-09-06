<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Elasticsearch Client Configuration
    |--------------------------------------------------------------------------
    |
    | This array will be passed to the Elasticsearch client.
    | See configuration options here:
    |
    | http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_configuration.html
    */

    'config' => [
        'hosts'   => ['localhost:9200'],
        'retries' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Index Name
    |--------------------------------------------------------------------------
    |
    | This is the index name that Elasticquent will use for all
    | Elasticquent models.
    */

    'default_index' => 'my_custom_index_name',

    /*
    |--------------------------------------------------------------------------
    | Enable to use Amazon Elasticsearch Service
    |--------------------------------------------------------------------------
    */
    'aws' => [
        'iam'    => true,
        'key'    => 'YOUR_AWS_KEY',
        'secret' => 'YOUR_AWS_SECRET',
        'region' => 'us-west-2',
    ],

];
