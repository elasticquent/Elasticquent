<?php

use Elasticquent\ElasticquentInterface;
use Elasticquent\ElasticquentTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Mockery as m;

class SearchTestModel extends Eloquent implements ElasticquentInterface
{
    use ElasticquentTrait;

    protected $table = 'test_table';

    public function getElasticSearchClient()
    {
        $elasticClient = m::mock('Elasticsearch\Client');

        $elasticClient
            ->shouldReceive('search')
            ->with(searchParams('with results'))
            ->andReturn(successfulResults());

        $elasticClient
            ->shouldReceive('search')
            ->with(searchParams('with no results'))
            ->andReturn(unsuccessfulResults());

        $elasticClient
            ->shouldReceive('search')
            ->with(searchParams(''))
            ->andReturn(unsuccessfulResults());

        $elasticClient
            ->shouldReceive('search')
            ->with(complexParameters())
            ->andReturn(successfulResults());

        return $elasticClient; 
    }
}
