<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class TestModel extends Eloquent implements \Elasticquent\ElasticquentInterface {

    use Elasticquent\ElasticquentTrait;

    protected $fillable = array('name');

    function getTable()
    {
        return 'testing';
    }
}

class CustomTestModel extends Eloquent implements \Elasticquent\ElasticquentInterface {

    use Elasticquent\ElasticquentTrait;

    protected $fillable = array('name');

    function getIndexDocumentData()
    {
        return array('foo' => 'bar');
    }
}
