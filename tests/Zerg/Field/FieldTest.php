<?php

namespace Zerg\Field;

use Zerg\DataSet;
use Zerg\Stream\StringStream;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zerg\Field\AbstractField::parseLengthWord
     * */
    public function testWordLength()
    {
        $field = new Int('semi_nibble');
        $this->assertEquals(2, $field->getLength());

        $field->setLength('short');
        $this->assertEquals(16, $field->getLength());
    }

    /**
     * @covers Zerg\Field\AbstractField::getLength
     * @covers Zerg\Field\AbstractField::setDataSet
     * */
    public function testConditionalLength()
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

        $field->setLength('/c');
        $this->assertEquals(8, $field->getLength());

        $field->setLength('/d/e/f');
        $this->assertEquals(16, $field->getLength());

        $field->setLength('/d/g');
        $this->assertEquals(4, $field->getLength());

    }

    /**
     * @covers Zerg\Field\AbstractField::getLength
     * */
    public function testLengthCallback()
    {
        $field = new Int(8, [
            'lengthCallback' => function($length) {
                return $length - 2;
            }
        ]);

        $this->assertEquals(6, $field->getLength());

        $field2 = new Int('short', [
            'lengthCallback' => function($length) {
                return $length * 2;
            }
        ]);

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

        $field2->setDataSet($dataSet);
        $this->assertEquals(32, $field2->getLength());
        $field2->setLength('/c');
        $this->assertEquals(16, $field2->getLength());
    }

    /**
     * @covers Zerg\Field\AbstractField::format
     * */
    public function testValueCallback()
    {
        $stream = new StringStream('123abcdefgqwertyahnytjssdadfkjhb');
        $field = new Int('byte', [
            'valueCallback' => function($value) {
                return $value - 2 * 8;
            }
        ]);

        $this->assertEquals(33, $field->read($stream));

    }
}