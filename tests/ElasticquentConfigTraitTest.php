<?php

class ElasticquentConfigTraitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->model = new TestModel;
    }

    public function testAccesssToConfig()
    {
        $this->assertEquals(['localhost:9200'], $this->model->getElasticConfig('config.hosts'));
        $this->assertEquals(1, $this->model->getElasticConfig('config.retries'));
        $this->assertEquals('my_custom_index_name', $this->model->getElasticConfig('default_index'));
    }
}
