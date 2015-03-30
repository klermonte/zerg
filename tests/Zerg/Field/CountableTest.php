<?php

namespace Zerg\Field;


use Zerg\DataSet;
use Zerg\Stream\StringStream;

class CountableTest extends \PHPUnit_Framework_TestCase
{
    public function arrays()
    {
        return [
            [['a' => ['b' => [10, 20]]], [10, 20]],
            [['a' => ['b' => ['/a/c', '/d'], 'c' => 5], 'd' => 30], [5, 30]],
            [['a' => ['b' => ['/a/c', '/d'], 'c' => '/a/b/1'], 'd' => 30], [30, 30]],
        ];
    }

    public function testArrayCount()
    {
        $field = new String(8);
        $field->setCount([2, 3]);

        $this->assertEquals([2, 3], $field->getCount());
    }

    /**
     * @dataProvider arrays
     */
    public function testArrayResolve($data, $count)
    {
        $dataSet = new DataSet($data);

        $field = new String(8);
        $field->setDataSet($dataSet);
        $field->setCount('/a/b');

        $this->assertEquals($count, $field->getCount());

    }

    public function testNestedCount()
    {
        $field = new Collection([
            'header' => ['string', 1, ['count' => [2, 3]]]
        ]);

        $stream = new StringStream('qweqweqweqweqweqwe');

        $dataSet = $field->parse($stream);

        $this->assertCount(2, $dataSet['header']);
        $this->assertCount(3, $dataSet['header'][0]);
        $this->assertCount(3, $dataSet['header'][1]);
    }

    public function testNestedBackLinkedCount()
    {
        $field = new Collection([
            'count' => ['string', 8],
            'header' => ['string', 8, ['count' => [2, '/count']]]
        ]);

        $stream = new StringStream('3qweqweqweqweqweqwe');

        $dataSet = $field->parse($stream);

        $this->assertCount(2, $dataSet['header']);
        $this->assertCount(3, $dataSet['header'][0]);
        $this->assertCount(3, $dataSet['header'][1]);
    }

    public function testNestedCollectionCount()
    {
        $field = new Collection([
            'header' => ['collection', [
                'first' => ['string', 1],
                'second' => ['string', 1, ['count' => 2]]
            ], ['count' => [2, 3]]]
        ]);

        $stream = new StringStream('qweqweqweqweqweqweqweqwe');

        $dataSet = $field->parse($stream);

        $this->assertCount(2, $dataSet['header']);
        $this->assertCount(3, $dataSet['header'][0]);
        $this->assertCount(3, $dataSet['header'][1]);
        $this->assertCount(2, $dataSet['header'][0][0]['second']);
    }
}