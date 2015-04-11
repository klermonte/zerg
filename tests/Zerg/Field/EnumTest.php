<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $field = new Enum(8, [
                49 => 'right',
                32 => 'wrong',
            ],
            ['default' => 'default']
        );

        $stream = new StringStream('123abcdefg');

        $this->assertEquals('right', $field->read($stream));
        $this->assertEquals('default', $field->read($stream));

    }

    /**
     * @expectedException \Zerg\Field\InvalidKeyException
     * */
    public function testKeyException()
    {
        $field = new Enum(8, [
            49 => 'right',
            32 => 'wrong',
        ]);

        $stream = new StringStream('223abcdefg');

        $field->read($stream);
    }

    public function testAssertion()
    {
        $field = new Enum(8, [
            49 => 'right',
            32 => 'wrong',
        ], ['assert' => 'right']);
        $this->assertTrue($field->validate($field->read(new StringStream('1'))));
    }

    /**
     * @expectedException \Zerg\Field\AssertException
     * */
    public function testAssertionException()
    {
        (new Enum(8, [
            49 => 'right',
            32 => 'wrong',
        ], ['assert' => 'wrong']))->parse(new StringStream('1'));
    }

}