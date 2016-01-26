<?php

class ElasticquentTraitTest extends PHPUnit_Framework_TestCase {

    public $modelData = array('name' => 'Test Name');

    /**
     * Testing Model
     *
     * @return void
     */
    public function setup()
    {
        $this->model = new TestModel;
        $this->model->fill($this->modelData);
    }

    /**
     * Test type name inferred from table name
     */
    public function testTypeNameInferredFromTableName()
    {
        $this->assertEquals('test_table', $this->model->getTypeName());
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
        $this->model->useTimestampsInIndex();
        $this->assertTrue($this->model->usesTimestampsInIndex());

        $this->model->dontUseTimestampsInIndex();
        $this->assertFalse($this->model->usesTimestampsInIndex());
    }

    /**
     * Testing Mapping Setup
     */
    public function testMappingSetup()
    {
        $mapping = array('foo' => 'bar');

        $this->model->setMappingProperties($mapping);
        $this->assertEquals($mapping, $this->model->getMappingProperties());
    }

    /**
     * Test Index Document Data
     */
    public function testIndexDocumentData()
    {
        // Basic
        $this->assertEquals($this->modelData, $this->model->getIndexDocumentData());

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
        $this->assertFalse($this->model->isDocument());
        $this->assertNull($this->model->documentScore());
    }

}
