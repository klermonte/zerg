<?php

namespace Zerg;

class DataSetTest extends \PHPUnit_Framework_TestCase
{

    public function testGetData()
    {
        $dataSet = new DataSet(['a' => 'b']);
        $data = $dataSet->getData();
        $this->assertArrayHasKey('a', $data);
        $this->assertEquals('b', $data['a']);
        $this->assertCount(1, $data);
    }

    public function testFlatSetValue()
    {
        $data = new DataSet();
        $data->setValue('foo', 'bar');
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
        $this->assertCount(1, $data);
    }

    public function testFlatGetValue()
    {
        $dataSet = new DataSet();
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValue('foo'));
    }

    public function testFlatGetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo'], true));
    }

    public function testFlatSetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValueByPath(['foo'], 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo'], true));
    }

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

    public function testNestedGetValue()
    {
        $dataSet = new DataSet();
        $dataSet->push('level1');
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValue('foo'));
    }

    public function testNestedGetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->push('level1');
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['level1', 'foo'], true));
    }

    public function testNestedSetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValueByPath(['level1', 'foo'], 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['level1', 'foo'], true));
    }
}