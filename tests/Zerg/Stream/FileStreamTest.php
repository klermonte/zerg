<?php

namespace Zerg\Stream;

class FileStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zerg\Stream\FileStream::read
     * @covers Zerg\Stream\FileStream::skip
     * */
    public function testRead()
    {
        $stream = new FileStream(dirname(__DIR__) . '/data');
        $this->assertEquals('1', $stream->read(8));
        $stream->skip(16);
        $this->assertEquals('a', $stream->read(8));
        $stream->skip(4);
        $this->assertEquals('&', $stream->read(8));
        $this->assertEquals('6', $stream->read(8));
    }
} 