<?php

namespace Zerg\Field;

use PhpBio\Endian;
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

    public function testMassConfig()
    {
        $formatter = function ($value) {
            return $value . ';';
        };
        $string1 = new String(30, ['assert' => 'qwer', 'endian' => Endian::ENDIAN_BIG, 'formatter' => $formatter]);
        $string2 = new String([
            'size' => 30,
            'assert' => 'qwer',
            'endian' => Endian::ENDIAN_BIG,
            'formatter' => $formatter
        ]);
        $this->assertEquals($string1, $string2);
    }

}