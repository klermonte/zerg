<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $string = new String(2);
        $stream = new StringStream('abc');
        $this->assertEquals('ab', $string->read($stream));
    }

    public function testUtfRead()
    {
        $string = new String(4, ['utf' => 1]);
        $stream = new StringStream("\xff\xfeabc");
        $this->assertEquals('ab', $string->read($stream));
    }
}