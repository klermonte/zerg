<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $field = new Enum(8, [
            'values' => [
                49 => 'right',
                32 => 'wrong',
            ],
            'default' => 'default'
        ]);

        $data = '123abcdefg';
        $stream = new StringStream($data);

        $this->assertEquals('right', $field->read($stream));
        $this->assertEquals('default', $field->read($stream));

    }
}