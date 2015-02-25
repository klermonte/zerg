<?php

namespace Zerg\Stream;

class FileStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $stream = new FileStream(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'data');
        $this->assertEquals('1', $stream->getReader()->readString(1));
        $stream->skip(16);
        $this->assertEquals('a', $stream->getReader()->readString(1));
        $stream->skip(4);
        $this->assertEquals('b', $stream->getReader()->readString(1));
        $this->assertEquals('c', $stream->getReader()->readString(1));
    }
} 