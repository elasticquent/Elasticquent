<?php

/*
 * In these tests we're interested only in testing the Elasticquent search methods logic
 * and its ability to interpret and format the results. We're not interested in testing
 * the ElasticSearch client or our ability to hit an ElasticSearch database. Getting that
 * right is the job of those nice people at Elastic.
 *
 * So searches are made against a test model containing a mock of the ElasticSearch client.
 * This mock accepts search parameters, converts them into a parameter array and returns
 * results in the format that we would expect if we were to hit a real database, and
 * specifically returns results consistent with the ElasticSearch PHP client version
 * 2.0 documentation.
 *
 * The Elasticquent method will then format the response and we test that the resulting 
 * Elasticquent results collection methods return the results we expect to verify this. 
 */ 

class ElasticSearchMethodsTest extends PHPUnit_Framework_TestCase
{
    protected $expectedHits = [
            'total' => 2,
            'max_score' => 0.7768564,
            'hits' => [
                [
                    '_index' => 'my_custom_index_name',
                    '_type' => 'test_table',
                    '_score' => 0.7768564,
                    '_source' => [
                        'name' => 'foo',
                    ]
                ],
                [
                    '_index' => 'my_custom_index_name',
                    '_type' => 'test_table',
                    '_score' => 0.5634561,
                    '_source' => [
                        'name' => 'bar',
                    ]
                ],
            ]
        ];

    public function setUp()
    {
        $this->model = new SearchTestModel;
    }

    public function testSuccessfulSearch()
    {
        $result = $this->model->search('with results');

        $this->assertInstanceOf('Elasticquent\ElasticquentResultCollection', $result);
        $this->assertEquals(2, $result->totalHits());
        $this->assertEquals(0.7768564, $result->maxScore());
        $this->assertEquals(['total' => 5,'successful' => 5,'unsuccessful' => 0], $result->getShards());
        $this->assertEquals(8, $result->took());
        $this->assertFalse($result->timedOut());
        $this->assertEquals($this->expectedHits, $result->getHits());
        $this->assertEmpty($result->getAggregations());
    }

    public function testUnsuccessfulSearch()
    {
        $result = $this->model->search('with no results');

        $expectedHits = [
            'total' => 0,
            'max_score' => null,
            'hits' => []
        ];

        $this->assertInstanceOf('Elasticquent\ElasticquentResultCollection', $result);
        $this->assertEquals(0, $result->totalHits());
        $this->assertNull($result->maxScore());
        $this->assertEquals(['total' => 5,'successful' => 5,'unsuccessful' => 0], $result->getShards());
        $this->assertEquals(4, $result->took());
        $this->assertFalse($result->timedOut());
        $this->assertEquals($expectedHits, $result->getHits());
        $this->assertEmpty($result->getAggregations());
    }

    public function testSearchWithEmptyParamters()
    {
        $this->model->search();
        $this->model->search(null);
        $this->model->search('');

        $this->addToAssertionCount(3);  // does not throw an exception
    }

    public function testComplexSearch()
    {
        $params = complexParameters();
        $result = $this->model->complexSearch($params);

        $this->assertInstanceOf('Elasticquent\ElasticquentResultCollection', $result);
        $this->assertEquals($this->expectedHits, $result->getHits());
    }
}
