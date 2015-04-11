<?php

namespace Zerg\Field;

use Zerg\Stream\StringStream;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $string = new String(16);
        $stream = new StringStream('abc');
        $this->assertEquals('ab', $string->read($stream));
    }

    public function testAssertion()
    {
        $string = new String(8, ['assert' => '1']);
        $this->assertTrue($string->validate('1'));
    }

    /**
     * @expectedException \Zerg\Field\AssertException
     * */
    public function testAssertionException()
    {
        (new String(8, ['assert' => '2']))->parse(new StringStream('1'));
    }

}