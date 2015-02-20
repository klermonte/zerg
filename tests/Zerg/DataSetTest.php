<?php

namespace Zerg;

class DataSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zerg\DataSet::getData
     */
    public function testGetData()
    {
        $data = new DataSet(['a' => 'b']);
        $this->assertArrayHasKey('a', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertCount(1, $data);
    }

    /**
     * @covers Zerg\DataSet::setValue
     */
    public function testFlatSetValue()
    {
        $data = new DataSet();
        $data->setValue('foo', 'bar');
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
        $this->assertCount(1, $data);
    }
    
    /**
     * @covers Zerg\DataSet::getValue
     */
    public function testFlatGetValue()
    {
        $dataSet = new DataSet();
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValue('foo'));
    }
    
    /**
     * @covers Zerg\DataSet::getValueByPath
     */
    public function testFlatGetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo'], true));
    }
    
    /**
     * @covers Zerg\DataSet::setValueByPath
     */
    public function testFlatSetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValueByPath(['foo'], 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo'], true));
    }
    
    /**
     * @covers Zerg\DataSet::push
     */
    public function testNestedSetValue()
    {
        $dataSet = new DataSet();
        $dataSet->push('level1');
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals([
            'level1' => [
                'foo' => 'bar'
            ]
        ], $dataSet->getData());
    }
    
    /**
     * @covers Zerg\DataSet::getValue
     */
    public function testNestedGetValue()
    {
        $dataSet = new DataSet();
        $dataSet->push('level1');
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValue('foo'));
    }
    
    /**
     * @covers Zerg\DataSet::getValueByPath
     */
    public function testNestedGetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->push('level1');
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['level1', 'foo'], true));
    }

    /**
     * @covers Zerg\DataSet::setValueByPath
     */
    public function testNestedSetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValueByPath(['level1', 'foo'], 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['level1', 'foo'], true));
    }
}