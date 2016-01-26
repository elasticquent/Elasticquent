<?php

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
     * Test type name inferred from table name
     */
    public function testTypeNameInferredFromTableName()
    {
        $model = $this->testingModel();
        $this->assertEquals('test_table', $model->getTypeName());
    }

    /**
     * Test type name overrides table name 
     */
    public function testTypeNameOverridesTableName()
    {
        $model = new TestModelWithCustomTypeName;
        $this->assertEquals('test_type_name', $model->getTypeName());
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
