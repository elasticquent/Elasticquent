# Elastiquent: Elasticsearch for Eloquent Laravel Models

Elastiquent makes working with Elasticsearch and Eloquent models easier by mapping them to Elasticsearch types. You can use the default settings or define how Elasticsearch should index and search your Eloquent models right in the model.

Elastiquent uses the [official Elasticsearch PHP API](https://github.com/elasticsearch/elasticsearch-php). To get started, you should have a basic knowledge of how ElasticSearch works (indexes, types, mappings, etc). This is meant for use with Elasticsearch 1.x.

## Setup

To get started, add Elastiquent to you composer.json file:

    "adamfairholm/elastiquent" => "master-dev"

Once you've run a `composer update`, add the Elastiquent trait to any Eloquent model that you want to be able to index in Elasticsearch:

```php
use Adamfairholm\Elastiquent\ElastiquentTrait;

class Book extends Eloquent {

    use ElastiquentTrait;

}
```

Now your Eloquent model has some extra methods that make it easier to index your model's data using Elasticsearch.

## Basic Usage

To index all the entries in an Eloquent model, use `addAllToIndex`:

    Book::addAllToIndex();

You can also index a collection of models:

    $books = Book::where('id', '<', 200)->get();
    $books->addToIndex();

You can index individual entries as well:

    $book = Book::find($id);
    $book->addToIndex();

### Setting the Type Name

By default, Elastiquent will use the table name for your model as the type name for indexing. If you'd like to override it, you can with the `getTypeName` function.


```php
function getTypeName()
{
    return 'custom\_type\_name';
}
```

### Setting Up / Tearing Down

If you don't want to manually set up your Elasticsearch types, you can do some basic set up and tear down commands via your Elastiquent models.

For instance, if you'd like to setup a model's type mapping, you can do:

    Book::putMapping($ignoreConflicts = true);

To delete a mapping

    Book::deleteMapping();

To rebuild (delete and re-add, useful when you make important changes to your mapping) the mapping:

    Book::rebuildMapping();

### Document IDs

Elastiquent will use whatever is set as the `primaryKey` for your Eloquent models as the id for your Elasticsearch documents.

### Document Data

By default, Elastiquent will use the entire attribute array for your Elasticsearch documents. However, if you want to customize how your search documents are structured, you can set a `getIndexDocumentData` function that returns you own custom document array.

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

### Setting Mapping Properties

You can set mapping properties in a

### More Options

By default, document sources are enabled. To turn document sources off, set a property in your Eloquent model:

    protected $enableDocumentSource = false;

_Note that you must rebuild your mapping and re-index for this to take effect._



To check if the type for the Elastiquent model exists yet, use `typeExists`:

    $typeExists = Book::typeExists();

### Using Elastiquent With Custom Collections

If you are using a custom collection with your Eloquent models, you just need to add the `ElastiquentCollectionTrait` to your collection so you can use `addToIndex`.

```php
class MyCollection extends \Illuminate\Database\Eloquent\Collection {

    use ElastiquentCollectionTrait;
}
```
