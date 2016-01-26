<?php

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestModel extends Eloquent implements ElasticquentInterface {

    use ElasticquentTrait;

    protected $fillable = array('name');

    function getTable()
    {
        return 'testing';
    }
}