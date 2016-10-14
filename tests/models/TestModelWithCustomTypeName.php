<?php

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestModelWithCustomTypeName extends Eloquent implements ElasticquentInterface
{
    use ElasticquentTrait;

    protected $table = 'test_table';

    protected $fillable = ['name'];

    public function getTypeName()
    {
        return 'test_type_name';
    }
}
