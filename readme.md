# Elasticquent

_Elasticsearch for Eloquent Laravel Models_

Elasticquent makes working with [Elasticsearch](http://www.elasticsearch.org/) and [Eloquent](http://laravel.com/docs/eloquent) models easier by mapping them to Elasticsearch types. You can use the default settings or define how Elasticsearch should index and search your Eloquent models right in the model.

Elasticquent uses the [official Elasticsearch PHP API](https://github.com/elasticsearch/elasticsearch-php). To get started, you should have a basic knowledge of how Elasticsearch works (indexes, types, mappings, etc).

# Elasticsearch Requirements

You must be running _at least_ Elasticsearch 1.0. Elasticsearch 0.9 and below *will not work* and are not supported.

## Contents

* [Overview](#overview)
    * [How Elasticquent Works](#how-elasticquent-works)
* [Setup](#setup)
    * [Elasticsearch Configuration](#elasticsearch-configuration)
    * [Indexes and Mapping](#indexes-and-mapping)
    * [Setting a Custom Index Name](#setting-a-custom-index-name)
    * [Setting a Custom Type Name](#setting-a-custom-type-name)
* [Indexing Documents](#indexing-documents)
* [Searching](#searching)
    * [Search Collections](#search-collections)
    * [Search Collection Documents](#search-collection-documents)
    * [Chunking results from Elastiquent](#chunking-results-from-elastiquent)
    * [Using the Search Collection Outside of Elasticquent](#using-the-search-collection-outside-of-elasticquent)
* [More Options](#more-options)
    * [Document Ids](#document-ids)
    * [Document Data](#document-data)
    * [Using Elasticquent With Custom Collections](#using-elasticquetn-with-custom-collections)
* [Roadmap](#roadmap)

## Reporting Issues

If you do find an issue, please feel free to report it with [GitHub's bug tracker](https://github.com/elasticquent/Elasticquent/issues) for this project.

Alternatively, fork the project and make a pull request :)

## Overview

Elasticquent allows you take an Eloquent model and easily index and search its contents in Elasticsearch.

```php
    $books = Book::where('id', '<', 200)->get();
    $books->addToIndex();
```

When you search, instead of getting a plain array of search results, you instead get an Eloquent collection with some special Elasticsearch functionality.

```php
    $books = Book::search('Moby Dick');
    echo $books->totalHits();
```

Plus, you can still use all the Eloquent collection functionality:

```php
    $books = $books->filter(function ($book) {
        return $book->hasISBN();
    });
```

Check out the rest of the documentation for how to get started using Elasticsearch and Elasticquent!

### How Elasticquent Works

When using a database, Eloquent models are populated from data read from a database table. With Elasticquent, models are populated by data indexed in Elasticsearch. The whole idea behind using Elasticsearch for search is that its fast and light, so you model functionality will be dictated by what data has been indexed for your document.

## Setup

Before you start using Elasticquent, make sure you've installed [Elasticsearch](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html).

To get started, add Elasticquent to you composer.json file:

    "elasticquent/elasticquent": "dev-master"

Once you've run a `composer update`, you need to register Laravel service provider, in your `config/app.php`:

```php
'providers' => [
    ...
    Elasticquent\ElasticquentServiceProvider::class,
],
```

We also provide a facade for elasticsearch-php client (which has connected using our settings), add following to your `config/app.php` if you need so.

```php
'aliases' => [
    ...
    'Es' => Elasticquent\ElasticquentElasticsearchFacade::class,
],
```

Then add the Elasticquent trait to any Eloquent model that you want to be able to index in Elasticsearch:

```php
use Elasticquent\ElasticquentTrait;

class Book extends Eloquent
{
    use ElasticquentTrait;
}
```

Now your Eloquent model has some extra methods that make it easier to index your model's data using Elasticsearch.

### Elasticsearch Configuration

By default, Elasticquent will connect to `localhost:9200` and use `default` as index name, you can change this and the other settings in the configuration file. You can add the `elasticquent.php` config file at `/app/config/elasticquent.php` for Laravel 4, or use the following Artisan command to publish the configuration file into your config directory for Laravel 5:

```shell
$ php artisan vendor:publish --provider="Elasticquent\ElasticquentServiceProvider"
```

```php
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
    | This is the index name that Elastiquent will use for all
    | Elastiquent models.
    */

    'default_index' => 'my_custom_index_name',

);

```

### Indexes and Mapping

While you can definitely build your indexes and mapping through the Elasticsearch API, you can also use some helper methods to build indexes and types right from your models.

If you want a simple way to create indexes, Elasticquent models have a function for that:

    Book::createIndex($shards = null, $replicas = null);

For custom analyzer, you can set an `indexSettings` property in your model and define the analyzers from there:

```php
    /**
     * The elasticsearch settings.
     *
     * @var array
     */
    protected $indexSettings = [
        'analysis' => [
            'char_filter' => [
                'replace' => [
                    'type' => 'mapping',
                    'mappings' => [
                        '&=> and '
                    ],
                ],
            ],
            'filter' => [
                'word_delimiter' => [
                    'type' => 'word_delimiter',
                    'split_on_numerics' => false,
                    'split_on_case_change' => true,
                    'generate_word_parts' => true,
                    'generate_number_parts' => true,
                    'catenate_all' => true,
                    'preserve_original' => true,
                    'catenate_numbers' => true,
                ]
            ],
            'analyzer' => [
                'default' => [
                    'type' => 'custom',
                    'char_filter' => [
                        'html_strip',
                        'replace',
                    ],
                    'tokenizer' => 'whitespace',
                    'filter' => [
                        'lowercase',
                        'word_delimiter',
                    ],
                ],
            ],
        ],
    ];

```

For mapping, you can set a `mappingProperties` property in your model and use some mapping functions from there:

```php
protected $mappingProperties = array(
   'title' => array(
        'type' => 'string',
        'analyzer' => 'standard'
    )
);
```

If you'd like to setup a model's type mapping based on your mapping properties, you can use:

```php
    Book::putMapping($ignoreConflicts = true);
```

To delete a mapping:

```php
    Book::deleteMapping();
```

To rebuild (delete and re-add, useful when you make important changes to your mapping) a mapping:

```php
    Book::rebuildMapping();
```

You can also get the type mapping and check if it exists.

```php
    Book::mappingExists();
    Book::getMapping();
```

### Setting a Custom Index Name

By default, Elasticquent will look for the `default_index` key within your configuration file(`config/elasticquent.php`). To set the default value for an index being used, you can edit this file and set the `default_index` key:

```php
return array(

   // Other configuration keys ...
   
   /*
    |--------------------------------------------------------------------------
    | Default Index Name
    |--------------------------------------------------------------------------
    |
    | This is the index name that Elastiquent will use for all
    | Elastiquent models.
    */
    
   'default_index' => 'my_custom_index_name',
);
```

If you'd like to have a more dynamic index, you can also override the default configuration with a `getIndexName` method inside your Eloquent model:

```php
function getIndexName()
{
    return 'custom_index_name';
}
```

Note: If no index was specified, Elasticquent will use a hardcoded string with the value of `default`.

### Setting a Custom Type Name

By default, Elasticquent will use the table name of your models as the type name for indexing. If you'd like to override it, you can with the `getTypeName` function.

```php
function getTypeName()
{
    return 'custom_type_name';
}
```

To check if the type for the Elasticquent model exists yet, use `typeExists`:

```php
    $typeExists = Book::typeExists();
```

## Indexing Documents

To index all the entries in an Eloquent model, use `addAllToIndex`:

```php
    Book::addAllToIndex();
```

You can also index a collection of models:

```php
    $books = Book::where('id', '<', 200)->get();
    $books->addToIndex();
```

You can index individual entries as well:

```php
    $book = Book::find($id);
    $book->addToIndex();
```

You can also reindex an entire model:

```php
    Book::reindex();
```

## Searching

There are three ways to search in Elasticquent. All three methods return a search collection.

### Simple term search

The first method is a simple term search that searches all fields.

```php
    $books = Book::search('Moby Dick');
```

### Query Based Search

The second is a query based search for more complex searching needs:

```php
    public static function searchByQuery($query = null, $aggregations = null, $sourceFields = null, $limit = null, $offset = null, $sort = null)
```

**Example:**

```php
    $books = Book::searchByQuery(array('match' => array('title' => 'Moby Dick')));
```
Here's the list of available parameters:

- `query` - Your ElasticSearch Query
- `aggregations` - The Aggregations you wish to return. [See Aggregations for details](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-aggregations.html).
- `sourceFields` - Limits returned set to the selected fields only
- `limit` - Number of records to return
- `offset` - Sets the record offset (use for paging results)
- `sort` - Your sort query

### Raw queries

The final method is a raw query that will be sent to Elasticsearch. This method will provide you with the most flexibility
when searching for records inside Elasticsearch:

```php
    $books = Book::complexSearch(array(
        'body' => array(
            'query' => array(
                'match' => array(
                    'title' => 'Moby Dick'
                )
            )
        )
    ));
```

This is the equivalent to:
```php
    $books = Book::searchByQuery(array('match' => array('title' => 'Moby Dick')));
```

### Search Collections

When you search on an Elasticquent model, you get a search collection with some special functions.

You can get total hits:

```php
    $books->totalHits();
```

Access the shards array:

```php
    $books->shards();
```

Access the max score:

```php
    $books->maxScore();
```

Access the timed out boolean property:

```php
    $books->timedOut();
```

And access the took property:

```php
    $books->took();
```

And access search aggregations - [See Aggregations for details](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-aggregations.html):

```php
    $books->getAggregations();
```

### Search Collection Documents

Items in a search result collection will have some extra data that comes from Elasticsearch. You can always check and see if a model is a document or not by using the `isDocument` function:

```php
    $book->isDocument();
```

You can check the document score that Elasticsearch assigned to this document with:

```php
    $book->documentScore();
```

### Chunking results from Elastiquent

Similar to `Illuminate\Support\Collection`, the `chunk` method breaks the Elasticquent collection into multiple, smaller collections of a given size:

```php
    $all_books = Book::searchByQuery(array('match' => array('title' => 'Moby Dick')));
    $books = $all_books->chunk(10);
```


### Using the Search Collection Outside of Elasticquent

If you're dealing with raw search data from outside of Elasticquent, you can use the Elasticquent search results collection to turn that data into a collection.

```php
$client = new \Elasticsearch\Client();

$params = array(
    'index' => 'default',
    'type'  => 'books'
);

$params['body']['query']['match']['title'] = 'Moby Dick';

$collection = Book::hydrateElasticsearchResult($client->search($params));

```

## More Options

### Document IDs

Elasticquent will use whatever is set as the `primaryKey` for your Eloquent models as the id for your Elasticsearch documents.

### Document Data

By default, Elasticquent will use the entire attribute array for your Elasticsearch documents. However, if you want to customize how your search documents are structured, you can set a `getIndexDocumentData` function that returns you own custom document array.

```php
function getIndexDocumentData()
{
    return array(
        'id'      => $this->id,
        'title'   => $this->title,
        'custom'  => 'variable'
    );
}
```
Be careful with this, as Elasticquent reads the document source into the Eloquent model attributes when creating a search result collection, so make sure you are indexing enough data for your the model functionality you want to use.

### Using Elasticquent With Custom Collections

If you are using a custom collection with your Eloquent models, you just need to add the `ElasticquentCollectionTrait` to your collection so you can use `addToIndex`.

```php
class MyCollection extends \Illuminate\Database\Eloquent\Collection
{
    use ElasticquentCollectionTrait;
}
```

## Roadmap

Elasticquent currently needs:

* Tests that mock ES API calls.
* Support for routes
