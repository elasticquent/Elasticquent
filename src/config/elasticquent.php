<?php

return array(

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
        'hosts'     => ['localhost:9200'],
        'retries'   => 1,
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
    | Toggle syncing of Eloquent Models to Elastic Index
    |--------------------------------------------------------------------------
    |
    | Update Elastic index each time a model is saved/created/deleted
    |
    */
    'sync' => false,

    /*
    |--------------------------------------------------------------------------
    | Eloquent Results
    |--------------------------------------------------------------------------
    | Options:
    |   True  : The results of ElasticSearch will be converted to actual eloquent models from live database
    |   False : The results of ElasticSearch will not query the live database but instead use the _source properties
    |           and convert to Eloquent model. Your ElasticSearch mapping must contain all information you need since
    |           methods will not be loaded if there are missing properties.
    |
    */
    'use_live' => false,
);
