<?php

namespace Zerg\Stream;

class StringStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \OutOfBoundsException
     * */
    public function testRead()
    {
        $stream = new StringStream('123abcdefg');
        $this->assertEquals('1', $stream->getReader()->read(8));
        $stream->skip(16);
        $this->assertEquals('a', $stream->getReader()->read(8));
        $stream->skip(8);
        $this->assertEquals('c', $stream->getReader()->read(8));
        $this->assertEquals('d', $stream->getReader()->read(8));
        $stream->getReader()->read(200 * 8);
    }
} 