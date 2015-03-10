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

    public function testReadBits()
    {
        $stream = new StringStream("\x73\xda\xf4\xdc\0");
        $int = new Int('nibble');
        $values = [3,7,10,13,4,15];
        foreach ($values as $expected) {
            $this->assertSame($expected, $int->read($stream));
        }

        $this->assertSame(0xdc, (new Int('byte'))->read($stream));

        $int->setSize('semi_nibble');
        $stream->getReader()->setPosition(0)->setCurrentBit(0);

        $values = [3,0,3,1,2,2,1,3,0,1,3,3];
        foreach ($values as $expected) {
            $this->assertSame($expected, $int->read($stream));
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     * */
    public function testOutOfBoundary()
    {
        $int = new Int('nibble');
        $newStream = new StringStream("\x31");
        $int->read($newStream);
        $this->assertSame(3, $int->read($newStream));
        $int->read($newStream);
    }

    /**
     * @expectedException \Zerg\Field\ConfigurationException
     * @dataProvider invalidValues
     * */
    public function testInvalidOptionSize($invalidValue)
    {
        $int = new Int($invalidValue);
        $seze = $int->getSize();
    }


    /**
     * @expectedException \Zerg\Field\ConfigurationException
     * @dataProvider invalidValues
     * */
    public function testInvalidOptionCount($invalidValue)
    {
        $int = new Int('byte', ['count' => $invalidValue]);
        $count = $int->getCount();
    }

    /**
     * @expectedException \Zerg\Field\ConfigurationException
     * */
    public function testLargeIntException()
    {
        $int = new Int(64);
        $int->read(new StringStream('foo'));
    }

    public function invalidValues()
    {
        return [
            [-1],
            ['/foo/bar']
        ];
    }
} 