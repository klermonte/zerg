<?php

namespace Zerg\Field;


use Zerg\DataSet;
use Zerg\Stream\StringStream;

class ArrTest extends \PHPUnit_Framework_TestCase
{
    public function testSetUntil()
    {
        $arr = new Arr(null, ['int', 'byte'], ['until' => 'eof']);
        $this->assertEquals('eof', $arr->getUntil());
        $arr->setUntil(function () {return true;});
        $this->assertInstanceOf('\Closure', $arr->getUntil());
    }

    public function testUntilEof()
    {
        $arr = new Arr(null, ['string', 'byte'], ['until' => 'eof']);
        $data = $arr->parse(new StringStream('12345'));
        $this->assertEquals(str_split('12345'), $data);
    }

    public function testUntilCallback()
    {
        $arr = new Arr(null, ['string', 'byte'], ['until' => function ($lastValue) {
            return (int) $lastValue < 5;
        }]);
        $data = $arr->parse(new StringStream('12345'));
        $this->assertEquals(str_split('12345'), $data);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testOutException()
    {
        (new Arr(null, ['string', 'byte'], ['until' => function ($lastValue) {
            return (int) $lastValue < 9;
        }]))->parse(new StringStream('12345'));
    }

    public function testBackLinkField()
    {
        $arr = new Arr(2, '/firstField');
        $arr->setDataSet(new DataSet(['firstField' => ['string', 8]]));
        $data = $arr->parse(new StringStream('12345'));
        $this->assertEquals('1', $data[0]);
        $this->assertEquals('2', $data[1]);
    }

    public function testMassConfig()
    {
        $conditional1 = new Arr(null, ['int', 8], ['until' => 'eof']);
        $conditional2 = new Arr([
            'field' => ['int', 8],
            'until' => 'eof',
        ]);
        $this->assertEquals($conditional1, $conditional2);
    }
}