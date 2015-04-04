<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $string = new String(16);
        $stream = new StringStream('abc');
        $this->assertEquals('ab', $string->read($stream));
    }
}