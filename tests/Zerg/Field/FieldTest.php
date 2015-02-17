<?php

namespace Zerg\Field;

use Zerg\DataSet;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testWordLength()
    {
        $field = new Int('semi_nibble');
        $this->assertEquals(2, $field->getLength());

        $field->setLength('short');
        $this->assertEquals(16, $field->getLength());
    }

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
}