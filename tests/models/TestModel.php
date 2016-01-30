<?php

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestModel extends Eloquent implements ElasticquentInterface {

    use ElasticquentTrait;

    protected $table = 'test_table';

    protected $fillable = array('name');
}