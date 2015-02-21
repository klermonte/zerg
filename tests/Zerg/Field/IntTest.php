<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class IntTest extends \PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $int = new Int(1, ['signed' => true]);
        $this->assertTrue($int->getSigned());
        $int->setSigned(false);
        $this->assertFalse($int->getSigned());
        $int->setSigned(true);
        $this->assertTrue($int->getSigned());
    }

    public function testRead()
    {
        $stream = new StringStream("\x03\x80\x80");
        $int = new Int('byte', ['signed' => true]);
        $this->assertSame(3, $int->read($stream));
        $this->assertSame(-128, $int->read($stream));
        $int->setSigned(false);
        $this->assertSame(128, $int->read($stream));
    }
} 