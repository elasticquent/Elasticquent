<?php

use PHPUnit\Framework\TestCase;

class ElasticquentClientTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new TestModel;
    }

    public function testClient()
    {
        $this->assertInstanceOf('Elastic\Elasticsearch\Client', $this->model->getElasticSearchClient());
    }
}
