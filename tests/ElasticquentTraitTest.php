<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class ElasticquentTraitTest extends PHPUnit_Framework_TestCase {

    public $modelData = array('name' => 'Test Name');

    /**
     * Testing Model
     *
     * @return void
     */
    public function testingModel()
    {
        $model = new TestModel;
        $model->fill($this->modelData);

        return $model;
    }

    /**
     * Test getTypeName()
     */
    public function testGetTypeName()
    {
        $model = $this->testingModel();
        $this->assertEquals('testing', $model->getTypeName());
    }

    /**
     * Test Basic Properties Getters
     */
    public function testBasicPropertiesGetters()
    {
        $model = $this->testingModel();

        $model->useTimestampsInIndex();
        $this->assertTrue($model->usesTimestampsInIndex());

        $model->dontUseTimestampsInIndex();
        $this->assertFalse($model->usesTimestampsInIndex());
    }

    /**
     * Testing Mapping Setup
     */
    public function testMappingSetup()
    {
        $model = $this->testingModel();

        $mapping = array('foo' => 'bar');

        $model->setMappingProperties($mapping);
        $this->assertEquals($mapping, $model->getMappingProperties());
    }

    /**
     * Test Index Document Data
     */
    public function testIndexDocumentData()
    {
        // Basic
        $model = $this->testingModel();
        $this->assertEquals($this->modelData, $model->getIndexDocumentData());

        // Custom
        $custom = new CustomTestModel();
        $custom->fill($this->modelData);

        $this->assertEquals(
                array('foo' => 'bar'), $custom->getIndexDocumentData());
    }

    /**
     * Test Document Null States
     */
    public function testDocumentNullStates()
    {
        $model = $this->testingModel();
        
        $this->assertFalse($model->isDocument());
        $this->assertNull($model->documentScore());
    }

}

class TestModel extends Eloquent implements \Elasticquent\ElasticquentInterface {

    use Elasticquent\ElasticquentTrait;

    protected $fillable = array('name');

    function getTable()
    {
        return 'testing';
    }
}

class CustomTestModel extends Eloquent implements \Elasticquent\ElasticquentInterface {

    use Elasticquent\ElasticquentTrait;

    protected $fillable = array('name');

    function getIndexDocumentData()
    {
        return array('foo' => 'bar');
    }
}
