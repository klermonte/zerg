<?php

namespace Zerg;

class DataSetTest extends \PHPUnit_Framework_TestCase
{

    public function testArrayAccess()
    {
        $dataSet = new DataSet(['a' => 'b']);
        $dataSet['c'] = 'd';
        $this->assertArrayHasKey('a', $dataSet);
        $this->assertArrayHasKey('c', $dataSet);
        $this->assertEquals('b', $dataSet['a']);
        $this->assertEquals('d', $dataSet['c']);
        $this->assertCount(2, $dataSet);
        unset($dataSet['a']);
        $this->assertArrayNotHasKey('a', $dataSet);
        $this->assertCount(1, $dataSet);
    }

    public function testIterator()
    {
        $dataSet = new DataSet([
            'a' => 'b',
            'c' => 'd',
            'e' => [
                'f' => 'e'
            ]
        ]);

        $dataSet->rewind();
        while($dataSet->valid()) {
            $this->assertSame($dataSet[$dataSet->key()], $dataSet->current());
            $dataSet->next();
        }
    }

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
        $dataSet->push('nextLevel');
        $this->assertEquals(null, $dataSet->getValue('foo'));
    }

    public function testFlatGetValueByPath()
    {
        $dataSet = new DataSet();
        $dataSet->setValue('foo', 'bar');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo']));
        $this->assertEquals(null, $dataSet->getValueByPath(['foo', 'bar']));
    }

    public function testFlatSetValueByPath()
    {
        $dataSet = new DataSet(['exists' => ['key' => 'value']]);
        $dataSet->setValueByPath(['foo'], 'bar');
        $dataSet->setValueByPath(['exists', 'key'], 'newValue');
        $this->assertEquals('bar', $dataSet->getValueByPath(['foo']));
        $this->assertEquals('newValue', $dataSet->getValueByPath(['exists', 'key']));
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