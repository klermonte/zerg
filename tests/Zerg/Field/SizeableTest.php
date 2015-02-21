<?php

namespace Zerg\Field;

use Zerg\DataSet;

class SizeableTest extends \PHPUnit_Framework_TestCase
{
    public function testWordLength()
    {
        $field = new Int('semi_nibble');
        $this->assertEquals(2, $field->getSize());

        $field->setSize('short');
        $this->assertEquals(16, $field->getSize());
    }

    public function testConditionalSize()
    {
        $field = new Int('semi_nibble');

        $dataSet = new DataSet([
            'a' => [
                'b' => 4
            ],
            'c' => 8,
            'd' => [
                'g' => '/a/b',
                'e' => [
                    'f' => 16
                ]
            ]
        ]);

        $field->setDataSet($dataSet);

        $field->setSize('/c');
        $this->assertEquals(8, $field->getSize());

        $field->setSize('/d/e/f');
        $this->assertEquals(16, $field->getSize());

        $field->setSize('/d/g');
        $this->assertEquals(4, $field->getSize());

    }

    public function testSizeCallback()
    {
        $field = new Int(8, [
            'sizeCallback' => function($length) {
                return $length - 2;
            }
        ]);

        $this->assertEquals(6, $field->getSize());
    }

} 