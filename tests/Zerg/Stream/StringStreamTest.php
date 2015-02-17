<?php

namespace Zerg\Stream;

class StringStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $data = '123abcdefg';
        $stream = new StringStream($data);
        $this->assertEquals('1', $stream->read(8));
    }
} 