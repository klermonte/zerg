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
        $this->assertEquals('1', $stream->getBuffer()->read(8));
        $stream->skip(16);
        $this->assertEquals('a', $stream->getBuffer()->read(8));
        $stream->skip(8);
        $this->assertEquals('c', $stream->getBuffer()->read(8));
        $this->assertEquals('d', $stream->getBuffer()->read(8));
        $stream->getBuffer()->read(200 * 8);
    }
} 