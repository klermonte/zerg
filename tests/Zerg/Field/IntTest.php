<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class IntTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Zerg\Field\Int::configure
     * @covers \Zerg\Field\Int::getSigned
     * @covers \Zerg\Field\Int::setSigned
     */
    public function testConfiguration()
    {
        $int = new Int(1, ['signed' => true]);
        $this->assertTrue($int->getSigned());
        $int->setSigned(false);
        $this->assertFalse($int->getSigned());
        $int->setSigned(true);
        $this->assertTrue($int->getSigned());
    }

    /**
     * @covers \Zerg\Field\Int::read
     * @covers \Zerg\Field\Int::int8
     * @covers \Zerg\Field\Int::uInt8
     */
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