<?php

namespace Zerg\Stream;

class FileStreamTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $stream = new FileStream(dirname(__DIR__) . '/data');
        $this->assertEquals('1', $stream->read(8));
    }
} 