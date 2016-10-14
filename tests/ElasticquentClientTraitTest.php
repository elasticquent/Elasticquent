<?php

class ElasticquentClientTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = new TestModel;
    }

    public function testClient()
    {
        $this->assertInstanceOf('ElasticSearch\Client', $this->model->getElasticSearchClient());
    }
}
