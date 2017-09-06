<?php

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class CustomTestModel extends Eloquent implements ElasticquentInterface
{
    use ElasticquentTrait;

    protected $fillable = ['name'];

    public function getIndexDocumentData()
    {
        return ['foo' => 'bar'];
    }
}
