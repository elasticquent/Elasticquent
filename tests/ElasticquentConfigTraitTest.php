<?php

/*
 * For this test we inject the trait directly into the test class.
 * This allows us to test a key methods that has visibility "protected".
 */

class ElasticquentConfigTraitTest extends PHPUnit_Framework_TestCase
{
    use Elasticquent\ElasticquentTrait;

    public function testAccesssToConfig()
    {
        $this->assertEquals(['localhost:9200'], $this->getElasticConfig('config.hosts'));
        $this->assertEquals(1, $this->getElasticConfig('config.retries'));
        $this->assertEquals('my_custom_index_name', $this->getElasticConfig('default_index'));
    }
}
