<?php

namespace Zerg\Stream;

class FileStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $stream = new FileStream(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'data');
        $this->assertEquals('1', $stream->getBuffer()->read(8));
        $stream->skip(16);
        $this->assertEquals('a', $stream->getBuffer()->read(8));
        $stream->skip(8);
        $this->assertEquals('c', $stream->getBuffer()->read(8));
        $this->assertEquals('d', $stream->getBuffer()->read(8));
    }
} 