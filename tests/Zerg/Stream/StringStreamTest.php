<?php

namespace Zerg\Stream;

class StringStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Zerg\Stream\EofException
     * */
    public function testRead()
    {
        $stream = new StringStream('123abcdefg');
        $this->assertEquals('1', $stream->read(8));
        $stream->skip(16);
        $this->assertEquals('a', $stream->read(8));
        $stream->skip(4);
        $this->assertEquals('&', $stream->read(8));
        $this->assertEquals('6', $stream->read(8));
        $stream->read(200);
    }
} 